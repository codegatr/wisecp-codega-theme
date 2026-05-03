<?php defined('CORE_FOLDER') OR exit('You can not get in here!'); ?>

<section class="cdg-page-head">
    <div class="cdg-container">
        <h1>Şifre Sıfırla</h1>
        <div class="breadcrumb">
            <a href="<?php echo APP_URI; ?>/">Anasayfa</a>
            <span class="sep">/</span>
            <span>Şifre Sıfırla</span>
        </div>
    </div>
</section>

<section class="cdg-section">
    <div class="cdg-container" style="max-width:480px;">

        <?php if(isset($error) && $error): ?>
            <div class="cdg-alert cdg-alert-error" style="margin-bottom:14px;"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(isset($success) && $success): ?>
            <div class="cdg-alert cdg-alert-success" style="margin-bottom:14px;"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($user_id) && $user_id):
            // === ASAMA 2: Yeni sifre belirleme (token ile gelinen sayfa) ===
        ?>
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#10b981,#34d399);color:#fff;display:inline-grid;place-items:center;font-size:28px;margin-bottom:12px;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h2 style="margin-bottom:6px;">Yeni Şifrenizi Belirleyin</h2>
                <p class="text-muted" style="font-size:13px;">Hesabınıza giriş yapmak için yeni bir şifre oluşturun.</p>
            </div>

            <form action="<?php echo isset($controller_link) ? htmlspecialchars($controller_link, ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" method="post" id="Reset_Password_Form">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('sign'); ?>

                <?php if(isset($by_name) && $by_name === 'mobile' && isset($security_question) && $security_question): ?>
                <div class="cdg-form-group">
                    <label class="cdg-form-label"><i class="bi bi-question-circle"></i> Güvenlik Sorusu</label>
                    <div style="font-size:13px;color:#475569;background:#f8fafc;padding:10px 12px;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:8px;">
                        <?php echo htmlspecialchars($security_question, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                    </div>
                    <input type="text" name="security_question_answer" class="cdg-form-control" placeholder="Cevabınız" required>
                </div>
                <?php endif; ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Yeni Şifre</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="cdg-rst-pw1" class="cdg-form-control" placeholder="En az 6 karakter" required minlength="6" autocomplete="new-password" oninput="cdgRstStrength()">
                        <button type="button" onclick="cdgRstToggle('cdg-rst-pw1','cdg-rst-eye1')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#64748b;cursor:pointer;padding:6px;">
                            <i class="bi bi-eye" id="cdg-rst-eye1"></i>
                        </button>
                    </div>
                    <div id="cdg-rst-strength" style="display:flex;gap:4px;margin-top:6px;">
                        <div class="cdg-rst-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                        <div class="cdg-rst-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                        <div class="cdg-rst-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                        <div class="cdg-rst-bar" style="flex:1;height:3px;background:#e2e8f0;border-radius:2px;"></div>
                    </div>
                    <div id="cdg-rst-strength-text" style="font-size:11px;color:#64748b;margin-top:4px;">Şifre gücü: -</div>
                </div>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">Yeni Şifre (Tekrar)</label>
                    <div style="position:relative;">
                        <input type="password" name="password_again" id="cdg-rst-pw2" class="cdg-form-control" placeholder="Şifreyi tekrar girin" required minlength="6" autocomplete="new-password" oninput="cdgRstMatch()">
                        <button type="button" onclick="cdgRstToggle('cdg-rst-pw2','cdg-rst-eye2')" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#64748b;cursor:pointer;padding:6px;">
                            <i class="bi bi-eye" id="cdg-rst-eye2"></i>
                        </button>
                    </div>
                    <div id="cdg-rst-match" style="font-size:11px;margin-top:4px;display:none;"></div>
                </div>

                <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;margin-top:8px;">
                    <i class="bi bi-check2-circle"></i> Sifremi Sifirla
                </button>
            </form>

            <div class="text-center" style="font-size:13px;color:var(--cdg-muted);margin-top:18px;">
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('sign-in') : '/sign-in'); ?>" style="font-weight:600;text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Girise don
                </a>
            </div>
        </div>

        <script>
        function cdgRstToggle(inpId, eyeId) {
            var inp = document.getElementById(inpId);
            var eye = document.getElementById(eyeId);
            if(!inp || !eye) return;
            if(inp.type === 'password') { inp.type = 'text'; eye.className = 'bi bi-eye-slash'; }
            else { inp.type = 'password'; eye.className = 'bi bi-eye'; }
        }
        function cdgRstStrength() {
            var pw = document.getElementById('cdg-rst-pw1').value;
            var bars = document.querySelectorAll('.cdg-rst-bar');
            var text = document.getElementById('cdg-rst-strength-text');
            var score = 0;
            if(pw.length >= 6) score++;
            if(pw.length >= 10) score++;
            if(/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
            if(/[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw)) score++;
            var labels = ['Cok zayif', 'Zayif', 'Orta', 'Iyi', 'Cok guclu'];
            var colors = ['#ef4444', '#f59e0b', '#fbbf24', '#10b981', '#059669'];
            bars.forEach(function(b, i) {
                b.style.background = (i < score) ? colors[score] : '#e2e8f0';
            });
            text.textContent = 'Şifre gücü: ' + (pw.length === 0 ? '-' : labels[score] || '-');
            text.style.color = pw.length === 0 ? '#64748b' : colors[score];
        }
        function cdgRstMatch() {
            var pw1 = document.getElementById('cdg-rst-pw1').value;
            var pw2 = document.getElementById('cdg-rst-pw2').value;
            var match = document.getElementById('cdg-rst-match');
            if(!pw2) { match.style.display = 'none'; return; }
            match.style.display = 'block';
            if(pw1 === pw2) {
                match.innerHTML = '<i class="bi bi-check-circle"></i> Sifreler eslesiyor';
                match.style.color = '#10b981';
            } else {
                match.innerHTML = '<i class="bi bi-x-circle"></i> Sifreler eslesmiyor';
                match.style.color = '#ef4444';
            }
        }
        document.getElementById('Reset_Password_Form').addEventListener('submit', function(e) {
            var pw1 = document.getElementById('cdg-rst-pw1').value;
            var pw2 = document.getElementById('cdg-rst-pw2').value;
            if(pw1 !== pw2) { e.preventDefault(); alert('Sifreler eslesmiyor!'); return false; }
            if(pw1.length < 6) { e.preventDefault(); alert('Sifre en az 6 karakter olmali!'); return false; }
        });
        </script>

        <?php else:
            // === ASAMA 1: Forget password (email gir) ===
        ?>
        <div class="cdg-card" style="padding:36px;">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#2E3B4E,#00D3E5);color:#fff;display:inline-grid;place-items:center;font-size:28px;margin-bottom:12px;">
                    <i class="bi bi-envelope-paper"></i>
                </div>
                <h2 style="margin-bottom:6px;">Sifrenizi mi unuttunuz?</h2>
                <p class="text-muted" style="font-size:13px;">E-posta adresinizi girin, size bir sıfırlama bağlantısı gönderelim.</p>
            </div>

            <form action="<?php echo isset($forget_password_link) ? htmlspecialchars($forget_password_link, ENT_QUOTES | ENT_HTML5, 'UTF-8') : ''; ?>" method="post" id="ForgotPassword_Form">
                <?php if(class_exists('Validation') && method_exists('Validation','get_csrf_token')) echo Validation::get_csrf_token('sign'); ?>

                <div class="cdg-form-group">
                    <label class="cdg-form-label">E-posta Adresi</label>
                    <input type="email" name="email" class="cdg-form-control" placeholder="ornek@email.com" required autofocus>
                </div>

                <button type="submit" class="cdg-btn cdg-btn-primary" style="width:100%;padding:13px;margin-top:8px;">
                    <i class="bi bi-envelope-arrow-up"></i> Sifirlama Baglantisi Gonder
                </button>
            </form>

            <div style="margin-top:18px;padding:12px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;color:#64748b;">
                <i class="bi bi-info-circle"></i>
                E-postaniz birkac dakika icinde gelmediyse spam klasorunuze bakmayi unutmayin.
            </div>

            <div class="text-center" style="font-size:13px;color:var(--cdg-muted);margin-top:18px;">
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('sign-in') : '/sign-in'); ?>" style="font-weight:600;text-decoration:none;">
                    <i class="bi bi-arrow-left"></i> Girise don
                </a>
                &nbsp;·&nbsp;
                <a href="<?php echo (class_exists('Controllers') && method_exists(Controllers::$init ?? null, 'CRLink') ? Controllers::$init->CRLink('sign-up') : '/sign-up'); ?>" style="font-weight:600;text-decoration:none;">
                    Hesap Olustur
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
