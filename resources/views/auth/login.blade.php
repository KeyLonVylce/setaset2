<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SETASET</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#004c99',
        });
    </script>
    @endif
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memproses login...</div>
    </div>
    
    <div class="login-wrapper">
        <!-- Left Side -->
        <div class="login-left">
            <div class="logo-container">
                <img src="{{ asset('pictures/jabar.png') }}" alt="Logo Jabar" class="logo-icon">
                <div class="logo-text">
                    <h1>SETASET</h1>
                    <p>Sistem Inventaris Aset</p>
                </div>
            </div>
            
            <div class="welcome-text">
                <h2>Selamat Datang di<br>Sistem Inventaris Aset</h2>
                <p>Kelola inventaris barang Dinas Komunikasi dan Informatika Provinsi Jawa Barat dengan mudah dan efisien.</p>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">Manajemen inventaris barang secara digital dan terstruktur</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">Pencatatan kondisi barang real-time untuk monitoring aset</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">Export laporan inventaris dalam format Excel dan PDF</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✓</div>
                    <div class="feature-text">Sistem pemindahan barang antar ruangan yang terintegrasi</div>
                </div>
            </div>
        </div>
        
        <!-- Right Side -->
        <div class="login-right">
            <div class="login-header">
                <h2>Masuk ke Sistem</h2>
                <p>Silakan login menggunakan akun Anda</p>
            </div>

            @if($errors->has('username'))
            <div class="alert alert-error">
                <span>⚠</span>
                <span>{{ $errors->first('username') }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div style="text-align:right; margin-top:10px;">
                    <a href="/forgot-password" style="color:#0d6efd; text-decoration:none;">
                        Lupa Password?
                    </a>
                </div>
                <br>
                <button type="submit" class="btn-login">Masuk ke Sistem</button>
            </form>

            
            <div class="footer-login">
                © {{ date('Y') }} Dinas Komunikasi dan Informatika Provinsi Jawa Barat<br>
                Hak Cipta Dilindungi Undang-Undang
            </div>
        </div>
    </div>
    
    <script>
        // Handle form submit dengan loading
        document.querySelector('form').addEventListener('submit', function(e) {
            // Tampilkan loading overlay
            document.getElementById('loadingOverlay').classList.add('active');
        });
        
        // Hide loading jika ada error (halaman reload dengan error)
        window.addEventListener('load', function() {
            const hasError = document.querySelector('.alert-error');
            if (hasError) {
                document.getElementById('loadingOverlay').classList.remove('active');
            }
        });
    </script>
</body>
</html>