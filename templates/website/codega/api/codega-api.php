<?php
/**
 * CODEGA Theme - Data API
 * 
 * Server-to-server endpoints used by codega.com.tr to fetch user data
 * (services, invoices, tickets summary). Authenticated with HMAC-SHA256.
 * 
 * Endpoints (all POST, JSON body):
 *   POST /codega-api/services     { email: "..." }
 *   POST /codega-api/invoices     { email: "..." }
 *   POST /codega-api/summary      { email: "..." }
 *   POST /codega-api/ping         { } - health check
 * 
 * Required headers:
 *   X-Codega-Timestamp:  unix timestamp
 *   X-Codega-Nonce:      random unique string
 *   X-Codega-Signature:  HMAC-SHA256(secret, "POST|/codega-api/services|{ts}|{nonce}|{sha256(body)}")
 */
defined('CORE_FOLDER') OR exit('You can not get in here!');

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'codega-bridge.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate');

function cg_api_response($status, $data = [], $message = '')
{
    http_response_code($status);
    echo json_encode([
        'ok'      => $status < 400,
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Method check
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    cg_api_response(405, [], 'Method not allowed');
}

// Read body
$body = file_get_contents('php://input') ?: '';
$json = json_decode($body, true);
if (!is_array($json)) $json = [];

// Resolve sub-endpoint from URL params (router passes params[1])
$endpoint = Filter::folder($params[1] ?? 'ping');

// Build canonical path for signature
$path = '/codega-api/' . $endpoint;

// HMAC validation
if (!Codega_Bridge::validateApi('POST', $path, $body)) {
    cg_api_response(401, [], 'Invalid or expired signature');
}

// Dispatch
switch ($endpoint) {

    case 'ping':
        cg_api_response(200, ['pong' => true, 'time' => time()], 'OK');
        break;

    case 'summary':
        $email = $json['email'] ?? '';
        if (!$email) cg_api_response(400, [], 'email required');

        try {
            $u = DB::row("SELECT id, name, surname, email, status, currency FROM users WHERE email = ? LIMIT 1", [$email]);
        } catch (Throwable $e) { $u = null; }

        if (!$u) cg_api_response(404, [], 'User not found');

        $uid = (int)$u['id'];
        try {
            $services_count = (int) DB::row("SELECT COUNT(*) c FROM products_orders WHERE owner_id = ? AND status = 'active'", [$uid])['c'];
            $unpaid_count   = (int) DB::row("SELECT COUNT(*) c FROM invoices WHERE owner_id = ? AND status = 'unpaid'", [$uid])['c'];
            $open_tickets   = (int) DB::row("SELECT COUNT(*) c FROM tickets WHERE owner_id = ? AND status IN ('open','answered')", [$uid])['c'];
        } catch (Throwable $e) {
            $services_count = $unpaid_count = $open_tickets = 0;
        }

        cg_api_response(200, [
            'user' => [
                'id'       => $uid,
                'name'     => trim(($u['name'] ?? '') . ' ' . ($u['surname'] ?? '')),
                'email'    => $u['email'],
                'status'   => $u['status'],
                'currency' => $u['currency'] ?? 'TRY',
            ],
            'counts' => [
                'active_services' => $services_count,
                'unpaid_invoices' => $unpaid_count,
                'open_tickets'    => $open_tickets,
            ],
        ], 'OK');
        break;

    case 'services':
        $email = $json['email'] ?? '';
        if (!$email) cg_api_response(400, [], 'email required');

        try {
            $u = DB::row("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
            if (!$u) cg_api_response(404, [], 'User not found');

            $rows = DB::rows(
                "SELECT id, name, status, total, currency, period, due_date, created_at
                 FROM products_orders
                 WHERE owner_id = ?
                 ORDER BY id DESC
                 LIMIT 50",
                [(int)$u['id']]
            );
        } catch (Throwable $e) {
            cg_api_response(500, [], 'DB error');
        }

        cg_api_response(200, ['services' => $rows ?: []], 'OK');
        break;

    case 'invoices':
        $email = $json['email'] ?? '';
        if (!$email) cg_api_response(400, [], 'email required');

        try {
            $u = DB::row("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
            if (!$u) cg_api_response(404, [], 'User not found');

            $rows = DB::rows(
                "SELECT id, total, currency, status, due_date, created_at, paid_date
                 FROM invoices
                 WHERE owner_id = ?
                 ORDER BY id DESC
                 LIMIT 50",
                [(int)$u['id']]
            );
        } catch (Throwable $e) {
            cg_api_response(500, [], 'DB error');
        }

        cg_api_response(200, ['invoices' => $rows ?: []], 'OK');
        break;

    default:
        cg_api_response(404, [], 'Unknown endpoint: ' . $endpoint);
}
