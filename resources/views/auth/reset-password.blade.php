<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Reset Password - Buat Password Baru</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth/reset-password.css') }}">
</head>
<body>
    <div class="form-card">
        <div class="card-inner">
            <div class="card-header">
                <i class="fas fa-lock"></i>
                <h2>Reset Password</h2>
            </div>
            <div class="sub-text">
                <i class="fas fa-shield-alt"></i> Buat password baru yang kuat
            </div>

            <form method="POST" action="/reset-password/{{ $token }}">
                @csrf

                <div class="input-group">
                    <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                    <input type="password" class="input-field" id="new_password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    <div class="hint"><i class="fas fa-info-circle"></i> Gunakan kombinasi huruf, angka & simbol</div>
                </div>

                <div class="input-group">
                    <label for="password_confirm"><i class="fas fa-check-circle"></i> Konfirmasi Password</label>
                    <input type="password" class="input-field" id="password_confirm" name="password_confirmation" placeholder="Ketik ulang password baru" required autocomplete="off">
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fas fa-sync-alt"></i> Reset Password
                </button>
                <div class="helper-link">
                    <a href="/login"><i class="fas fa-sign-in-alt"></i> Ke Halaman Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const resetForm = document.querySelector('form');
            const passwordInput = resetForm.querySelector('input[name="password"]');
            const confirmInput = resetForm.querySelector('input[name="password_confirmation"]');

            if (passwordInput && confirmInput) {
                // Buat elemen pesan error
                let errorSpan = document.createElement('div');
                errorSpan.style.fontSize = '0.7rem';
                errorSpan.style.marginTop = '0.4rem';
                errorSpan.style.paddingLeft = '0.5rem';
                errorSpan.style.display = 'flex';
                errorSpan.style.alignItems = 'center';
                errorSpan.style.gap = '4px';
                errorSpan.style.color = '#e11d48';
                errorSpan.style.fontWeight = '500';
                errorSpan.style.borderRadius = '12px';
                errorSpan.style.backgroundColor = '#fff0f3';
                errorSpan.style.padding = '0.3rem 0.8rem';
                errorSpan.style.width = 'fit-content';

                const confirmGroup = confirmInput.closest('.input-group');
                if (confirmGroup && !confirmGroup.querySelector('.match-error')) {
                    errorSpan.className = 'match-error';
                    confirmGroup.appendChild(errorSpan);
                }

                function validateMatch() {
                    const pass = passwordInput.value;
                    const confirm = confirmInput.value;
                    if (confirm.length > 0 && pass !== confirm) {
                        errorSpan.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size: 0.65rem;"></i> Password dan konfirmasi tidak cocok';
                        errorSpan.style.display = 'flex';
                        confirmInput.style.borderColor = '#f97316';
                        confirmInput.style.boxShadow = '0 0 0 2px rgba(249,115,22,0.2)';
                    } else if (confirm.length > 0 && pass === confirm) {
                        errorSpan.innerHTML = '<i class="fas fa-check-circle" style="color:#10b981;"></i> Password cocok';
                        errorSpan.style.color = '#0e6b3e';
                        errorSpan.style.backgroundColor = '#e3f7ec';
                        confirmInput.style.borderColor = '#10b981';
                        confirmInput.style.boxShadow = '0 0 0 2px rgba(16,185,129,0.2)';
                    } else {
                        errorSpan.style.display = 'none';
                        confirmInput.style.borderColor = '#e2e8f0';
                        confirmInput.style.boxShadow = 'none';
                    }
                }

                passwordInput.addEventListener('input', validateMatch);
                confirmInput.addEventListener('input', validateMatch);

                resetForm.addEventListener('submit', function(e) {
                    if (passwordInput.value !== confirmInput.value) {
                        e.preventDefault();
                        errorSpan.innerHTML = '<i class="fas fa-ban"></i> Harap pastikan password dan konfirmasi sama!';
                        errorSpan.style.display = 'flex';
                        errorSpan.style.color = '#b91c1c';
                        errorSpan.style.backgroundColor = '#ffe4e6';
                        confirmInput.focus();
                        confirmInput.style.borderColor = '#dc2626';
                        return false;
                    }
                    if (passwordInput.value.length > 0 && passwordInput.value.length < 6) {
                        e.preventDefault();
                        alert('⚠️ Password minimal 6 karakter untuk keamanan yang lebih baik.');
                        passwordInput.focus();
                        return false;
                    }
                    return true;
                });
            }
        })();
    </script>
</body>
</html>