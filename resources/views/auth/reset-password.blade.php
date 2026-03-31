<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Reset Password - Buat Password Baru</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }

        .form-card {
            max-width: 480px;
            width: 100%;
            background: #ffffff;
            border-radius: 2rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(0, 0, 0, 0.02);
            transition: transform 0.25s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .form-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 28px 40px -16px rgba(0, 0, 0, 0.12);
        }

        .card-inner {
            padding: 2rem 1.8rem 2.2rem 1.8rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.8rem;
            border-bottom: 2px solid #eff3f8;
            padding-bottom: 1rem;
        }

        .card-header i {
            font-size: 1.9rem;
            color: #3b82f6;
            background: #eff6ff;
            padding: 10px;
            border-radius: 18px;
        }

        .card-header h2 {
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: -0.3px;
            background: linear-gradient(135deg, #1e293b, #2d3a4e);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin: 0;
        }

        .sub-text {
            font-size: 0.85rem;
            color: #5b6e8c;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
            background: #f8fafc;
            padding: 0.5rem 0.75rem;
            border-radius: 14px;
            display: inline-block;
        }

        .token-note {
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            color: #2c3e66;
            display: inline-block;
            margin-bottom: 1rem;
            font-family: monospace;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1f2a44;
            margin-bottom: 0.5rem;
        }

        .input-group label i {
            width: 20px;
            color: #3b82f6;
            margin-right: 6px;
        }

        .input-field {
            width: 100%;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            background-color: #ffffff;
            transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
            outline: none;
            color: #0f172a;
            font-weight: 500;
        }

        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12);
        }

        .input-field::placeholder {
            color: #a0b3d9;
            font-weight: 400;
        }

        .hint {
            font-size: 0.7rem;
            margin-top: 0.35rem;
            color: #5e6f8d;
        }

        .btn-reset {
            width: 100%;
            background: linear-gradient(105deg, #2563eb, #1e40af);
            border: none;
            padding: 0.9rem 1rem;
            border-radius: 40px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: white;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 0.6rem;
            box-shadow: 0 8px 18px -8px rgba(37, 99, 235, 0.35);
        }

        .btn-reset i {
            font-size: 1rem;
            transition: transform 0.2s;
        }

        .btn-reset:hover {
            background: linear-gradient(105deg, #1d4ed8, #1e3a8a);
            transform: scale(1.01);
            box-shadow: 0 12px 22px -10px rgba(37, 99, 235, 0.45);
        }

        .btn-reset:active {
            transform: scale(0.98);
        }

        .helper-link {
            margin-top: 1.6rem;
            text-align: center;
            font-size: 0.8rem;
        }

        .helper-link a {
            text-decoration: none;
            color: #3b82f6;
            font-weight: 500;
            transition: color 0.2s;
        }

        .helper-link a:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }

        hr {
            margin: 1rem 0;
            border: none;
            border-top: 1px solid #eef2f8;
        }

        @media (max-width: 500px) {
            .card-inner {
                padding: 1.5rem;
            }
            .card-header h2 {
                font-size: 1.4rem;
            }
        }
    </style>
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