<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SETASET - Inventaris Diskominfo')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        .header { background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); padding: 15px 30px; color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { font-size: 32px; font-weight: bold; letter-spacing: 2px; }
        .user-info { display: flex; align-items: center; gap: 20px; }
        .user-name { font-size: 14px; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; display: flex; align-items: center; gap: 8px; }
        .role-badge { padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .role-admin { background: #28a745; color: white; }
        .role-staff { background: #6c757d; color: white; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn-primary { background: #ff7b3d; color: white; }
        .btn-primary:hover { background: #ff6524; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .alert { padding: 15px 20px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #ff9a56; color: white; font-weight: 600; }
        table tr:hover { background: #f9f9f9; }
        .bell-icon { font-size: 24px; cursor: pointer; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .close { font-size: 28px; font-weight: bold; cursor: pointer; color: #999; }
        .close:hover { color: #333; }
    </style>
    @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <div class="header">
        <h1>SETASET</h1>
        <div class="user-info">
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <a href="{{ route('staff.index') }}" class="btn btn-warning">
                    👥 Kelola Staff
                </a>
            @endif
            <span class="user-name">
                {{ Auth::guard('stafaset')->user()->nama }}
                <span class="role-badge role-{{ Auth::guard('stafaset')->user()->role }}">
                    {{ Auth::guard('stafaset')->user()->role_label }}
                </span>
            </span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
            <a href="{{ route('notifications.index') }}" class="nav-link">
                🔔
                <span id="notif-count" class="badge bg-danger d-none"></span>
            </a>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @yield('content')
    </div>
    @yield('scripts')
</body>

<script>
setInterval(() => {
    fetch('{{ route('notifications.realtime') }}')
        .then(res => res.json())
        .then(data => {
            let badge = document.getElementById('notif-count');
            if (data.unread > 0) {
                badge.innerText = data.unread;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });
}, 5000); // 5 detik
</script>

</html>

