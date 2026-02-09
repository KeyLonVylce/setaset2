<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SETASET</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 102, 204, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .loading-text {
            color: white;
            margin-top: 20px;
            font-size: 16px;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .login-left {
            background-image: url('{{ asset('pictures/gedungdiskominfo.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 60px 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Dark overlay for better text readability */
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(0, 102, 204, 0.65) 0%, 
                rgba(0, 76, 153, 0.60) 100%);
            z-index: 0;
        }
        
        /* Subtle pattern overlay for depth */
        .login-left::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.08) 0%, transparent 50%);
            z-index: 0;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            padding: 8px;
            backdrop-filter: blur(10px);
        }
        
        .logo-text h1 {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 5px;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5), 0 0 30px rgba(0,0,0,0.3);
        }
        
        .logo-text p {
            font-size: 14px;
            opacity: 0.95;
            text-shadow: 0 1px 10px rgba(0,0,0,0.5), 0 0 20px rgba(0,0,0,0.3);
        }
        
        .welcome-text {
            position: relative;
            z-index: 1;
        }
        
        .welcome-text h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
            line-height: 1.3;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5), 0 0 30px rgba(0,0,0,0.3);
        }
        
        .welcome-text p {
            font-size: 15px;
            line-height: 1.6;
            opacity: 0.95;
            text-shadow: 0 1px 10px rgba(0,0,0,0.5), 0 0 20px rgba(0,0,0,0.3);
        }
        
        .features {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        
        .feature-item {
            display: flex;
            align-items: start;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .feature-icon {
            width: 24px;
            height: 24px;
            background: rgba(255,255,255,0.35);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .feature-text {
            font-size: 14px;
            line-height: 1.5;
            opacity: 0.98;
            text-shadow: 0 1px 8px rgba(0,0,0,0.4), 0 0 15px rgba(0,0,0,0.2);
        }
        
        .login-right {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header h2 {
            font-size: 28px;
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 14px;
        }
        
        .alert {
            padding: 14px 16px;
            margin-bottom: 25px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            border-left: 4px solid;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }
        
        .info-box {
            margin-top: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .info-box h4 {
            color: #1f2937;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .info-box p {
            margin: 6px 0;
            font-size: 13px;
            color: #4b5563;
        }
        
        .info-box strong {
            color: #0066cc;
            font-weight: 600;
        }
        
        .footer-login {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        @media (max-width: 968px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 500px;
            }
            
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .login-right {
                padding: 30px 20px;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
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