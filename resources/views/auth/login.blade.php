<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SETASET</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h1 { font-size: 36px; color: #ff7b3d; font-weight: bold; letter-spacing: 2px; }
        .login-header p { color: #666; margin-top: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: #ff7b3d; }
        .btn-login { width: 100%; padding: 12px; background: #ff7b3d; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background: #ff6524; }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 8px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info-box { margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 13px; color: #666; }
        .info-box h4 { color: #333; margin-bottom: 10px; }
        .info-box p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>SETASET</h1>
            <p>Sistem Inventaris Diskominfo</p>
        </div>

        @if($errors->has('username'))
        <div class="alert alert-error">{{ $errors->first('username') }}</div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="info-box">
            <h4>Akun Default:</h4>
            <p><strong>Admin:</strong> admin / admin123</p>
            <p><strong>Staff:</strong> staff / staff123</p>
        </div>
    </div>
</body>
</html>