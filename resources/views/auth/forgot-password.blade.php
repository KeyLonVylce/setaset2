<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Lupa Password - Kirim Link Reset</title>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth/forgot-password.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <!-- form forgot password -->
    <div class="form-card">
        <div class="card-inner">
            <div class="card-header">
                <i class="fas fa-key"></i>
                <h2>Lupa Password</h2>
            </div>
            <!-- Hanya Text Kirim tautan reset ke email Anda -->
            <div class="sub-text">
                <i class="fas fa-envelope-open-text" style="font-size: 0.75rem; margin-right: 6px;"></i> 
                Kirim tautan reset ke email Anda
            </div>

            <!-- Form untuk input email -->
            <form method="POST" action="/forgot-password"> <!-- method post untuk forgot password -->
                @csrf
                <div class="input-group"> <!-- input email -->
                    <label for="email_forgot"><i class="fas fa-at"></i> Alamat Email</label>
                    <input type="email" class="input-field" id="email_forgot" name="email" placeholder="contoh@email.com" required autocomplete="email">
                </div>
               
                <!-- Button untuk submit form -->
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
                
                <!-- Link untuk kembali ke halaman login -->
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

<!-- munculkan SweetAlert berdasarkan session (Sukses) -->
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

<!-- munculkan SweetAlert berdasarkan session (Error) -->
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