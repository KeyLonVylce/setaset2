<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Lupa Password - Kirim Link Reset</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .btn-oke:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }

        .btn-oke {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }
    </style>
</head>
<body>
    <div class="form-card">
        <div class="card-inner">
            <div class="card-header">
                <i class="fas fa-key"></i>
                <h2>Lupa Password</h2>
            </div>
            <div class="sub-text">
                <i class="fas fa-envelope-open-text" style="font-size: 0.75rem; margin-right: 6px;"></i> 
                Kirim tautan reset ke email Anda
            </div>

            <form method="POST" action="/forgot-password">
                @csrf
                <div class="input-group">
                    <label for="email_forgot"><i class="fas fa-at"></i> Alamat Email</label>
                    <input type="email" class="input-field" id="email_forgot" name="email" placeholder="contoh@email.com" required autocomplete="email">
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
                <div class="helper-link">
                    <a href="/login"><i class="fas fa-chevron-left"></i> Kembali ke halaman masuk</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Validasi email tetap
    const form = document.querySelector('form');
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const emailPattern = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
            if (this.value.length > 0 && !emailPattern.test(this.value)) {
                this.style.borderColor = '#f97316';
                this.style.boxShadow = '0 0 0 2px rgba(249,115,22,0.1)';
            } else {
                this.style.borderColor = '#e2e8f0';
                this.style.boxShadow = 'none';
            }
        });
    }

    // ✅ SweetAlert hanya muncul jika ada session
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Link reset password telah dikirim, cek email kamu!',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn-oke'
        },
        buttonsStyling: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn-oke'
        },
        buttonsStyling: false
    });
</script>
@endif
</body>
</html>