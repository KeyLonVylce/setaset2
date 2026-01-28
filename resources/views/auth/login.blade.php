<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SETASET</title>

    <!-- ================= hCAPTCHA ================= -->
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 420px;
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h1 {
            font-size: 36px;
            color: #ff7b3d;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .login-header p { color: #666; margin-top: 10px; }

        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ff7b3d;
        }

        .captcha-container {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #ff7b3d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-login:hover { background: #ff6524; }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .info-box {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
        }
        .info-box h4 { color: #333; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="login-container">

    <div class="login-header">
        <h1>SETASET</h1>
        <p>Sistem Inventaris Diskominfo</p>
    </div>

    {{-- Error --}}
    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
        @csrf

        <div class="form-group">
            <label>Username</label>
            <input type="text"
                   name="username"
                   value="{{ old('username') }}"
                   required
                   pattern="[a-zA-Z0-9_]+"
                   maxlength="50">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password"
                   name="password"
                   required
                   minlength="6">
        </div>

        <!-- ================= hCAPTCHA WIDGET ================= -->
        <div class="captcha-container">
            <div class="h-captcha"
                 data-sitekey="989a8fe0-68ea-4cc6-a2c4-a0ea0640be78">
            </div>
        </div>

        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="info-box">
        <h4>Akun Default:</h4>
        <p><strong>Admin:</strong> admin / admin123</p>
        <p><strong>Staff:</strong> staff / staff123</p>
    </div>

</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function (e) {
    if (typeof hcaptcha !== 'undefined') {
        if (!hcaptcha.getResponse()) {
            e.preventDefault();
            alert('Silakan verifikasi CAPTCHA');
        }
    }
});
</script>

</body>
</html>
