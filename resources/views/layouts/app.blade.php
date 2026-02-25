<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SETASET - Inventaris Diskominfo')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* ============================================
           TOAST NOTIFICATION SYSTEM
        ============================================ */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 420px;
            width: calc(100vw - 48px);
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: white;
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.07),
                0 10px 40px -5px rgba(0, 0, 0, 0.12),
                0 0 0 1px rgba(0, 0, 0, 0.04);
            animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        .toast.hiding {
            animation: toastSlideOut 0.3s ease-in forwards;
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(calc(100% + 24px)) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastSlideOut {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
                max-height: 200px;
                margin-bottom: 0;
            }
            to {
                opacity: 0;
                transform: translateX(calc(100% + 24px)) scale(0.95);
                max-height: 0;
                margin-bottom: -12px;
                padding-top: 0;
                padding-bottom: 0;
            }
        }

        /* Progress bar */
        .toast::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            border-radius: 0 0 14px 14px;
            animation: toastProgress 4.5s linear forwards;
        }

        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* Toast variants */
        .toast-success { border-left: 4px solid #10b981; }
        .toast-success::after { background: #10b981; }
        .toast-success .toast-icon-wrap { background: #d1fae5; color: #10b981; }

        .toast-error { border-left: 4px solid #ef4444; }
        .toast-error::after { background: #ef4444; }
        .toast-error .toast-icon-wrap { background: #fee2e2; color: #ef4444; }

        .toast-warning { border-left: 4px solid #f59e0b; }
        .toast-warning::after { background: #f59e0b; }
        .toast-warning .toast-icon-wrap { background: #fef3c7; color: #f59e0b; }

        .toast-info { border-left: 4px solid #0066cc; }
        .toast-info::after { background: #0066cc; }
        .toast-info .toast-icon-wrap { background: #dbeafe; color: #0066cc; }

        .toast-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .toast-body {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .toast-message {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            font-size: 18px;
            padding: 2px;
            flex-shrink: 0;
            transition: color 0.2s;
            line-height: 1;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .toast-close:hover {
            color: #374151;
            background: #f3f4f6;
        }

        /* ============================================
           HEADER STYLES
        ============================================ */
        .header { 
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            padding: 0;
            color: white; 
            box-shadow: 0 4px 20px rgba(0, 102, 204, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-jabar {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .header-text h1 { 
            font-size: 28px; 
            font-weight: 700; 
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }
        
        .header-text p {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .notification-bell {
            position: relative;
            background: rgba(255,255,255,0.15);
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
            font-size: 20px;
        }
        
        .notification-bell:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }
        
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #ff4757;
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
        
        .user-info { 
            display: flex; 
            align-items: center; 
            gap: 12px;
            background: rgba(255,255,255,0.15);
            padding: 8px 15px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name { 
            font-size: 14px; 
            font-weight: 600;
            line-height: 1.2;
        }
        
        .role-badge { 
            padding: 2px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: 600;
            display: inline-block;
            margin-top: 2px;
        }
        
        .role-admin { background: #10b981; color: white; }
        .role-staff { background: #6366f1; color: white; }
        
        /* ============================================
           BUTTON STYLES
        ============================================ */
        .btn { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 14px;
            font-weight: 500;
            text-decoration: none; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }
        
        .btn-danger { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .btn-success { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .btn-success:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-warning { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .btn-warning:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        
        /* ============================================
           CONTAINER & CARD
        ============================================ */
        .container { 
            max-width: 1400px; 
            margin: 30px auto; 
            padding: 0 30px; 
            flex: 1;
        }
        
        .card { 
            background: white; 
            border-radius: 16px; 
            padding: 30px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid rgba(0, 102, 204, 0.1);
        }
        
        /* ============================================
           LEGACY ALERT (keep for inline use if needed)
        ============================================ */
        .alert { 
            padding: 15px 20px; 
            margin-bottom: 20px; 
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid;
        }
        
        .alert-success { 
            background: #d1fae5; 
            color: #065f46; 
            border-color: #10b981;
        }
        
        .alert-error { 
            background: #fee2e2; 
            color: #991b1b; 
            border-color: #ef4444;
        }
        
        /* ============================================
           FORM STYLES
        ============================================ */
        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #1f2937; 
            font-size: 14px;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea { 
            width: 100%; 
            padding: 12px 16px; 
            border: 2px solid #e5e7eb; 
            border-radius: 8px; 
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        
        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus { 
            outline: none; 
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }
        
        .form-group textarea { resize: vertical; min-height: 100px; }
        
        /* ============================================
           TABLE STYLES
        ============================================ */
        table { 
            width: 100%; 
            border-collapse: separate;
            border-spacing: 0;
            background: white;
        }
        
        table th, table td { 
            padding: 14px 12px; 
            text-align: left; 
            border-bottom: 1px solid #f3f4f6;
        }
        
        table th { 
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            color: white; 
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        table th:first-child { border-top-left-radius: 8px; }
        table th:last-child { border-top-right-radius: 8px; }
        
        table tbody tr { transition: all 0.2s; }
        table tbody tr:hover { background: #f9fafb; }
        
        /* ============================================
           MODAL STYLES
        ============================================ */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
        
        .modal-content { 
            background: white; 
            margin: 5% auto; 
            padding: 0;
            border-radius: 16px; 
            width: 90%; 
            max-width: 600px; 
            max-height: 80vh; 
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 25px 30px;
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            color: white;
        }
        
        .modal-header h3 {
            font-size: 20px;
            font-weight: 600;
        }
        
        .modal-body {
            padding: 30px;
            max-height: calc(80vh - 80px);
            overflow-y: auto;
        }
        
        .close { 
            font-size: 28px; 
            font-weight: 300;
            cursor: pointer; 
            color: white;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .close:hover { opacity: 1; }

        /* ============================================
           CONFIRM DIALOG (custom)
        ============================================ */
        .confirm-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 99998;
            align-items: center;
            justify-content: center;
        }

        .confirm-overlay.active {
            display: flex;
        }

        .confirm-box {
            background: white;
            border-radius: 20px;
            padding: 32px;
            width: 90%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            animation: confirmPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            text-align: center;
        }

        @keyframes confirmPop {
            from { opacity: 0; transform: scale(0.85) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .confirm-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .confirm-icon.danger { background: #fee2e2; }
        .confirm-icon.warning { background: #fef3c7; }

        .confirm-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .confirm-box p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .confirm-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .confirm-actions .btn {
            min-width: 130px;
            justify-content: center;
        }
        
        /* ============================================
           FOOTER STYLES
        ============================================ */
        .footer {
            width: 100%;
            background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%);
            border-top: 3px solid #0066cc;
            margin-top: auto;
            box-shadow: 0 -4px 20px rgba(0, 102, 204, 0.08);
        }
        
        .footer-container {
            max-width: 1400px;
            padding: 40px 30px 25px;
            margin: 0 auto;
        }
        
        .footer-content {
            display: flex;
            align-items: flex-start;
            gap: 35px;
            margin-bottom: 25px;
        }
        
        .footer-logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 25px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f2ff 100%);
            border-radius: 12px;
            border: 2px solid rgba(0, 102, 204, 0.1);
        }
        
        .footer-logo-section img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        
        .footer-divider {
            width: 2px;
            height: 65px;
            background: linear-gradient(to bottom, transparent, #0066cc, transparent);
            opacity: 0.3;
        }
        
        .footer-title-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .footer-title {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-subtitle {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        
        .footer-info { flex: 1; padding-left: 10px; }
        
        .footer-info-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-info-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(to bottom, #0066cc, #004c99);
            border-radius: 2px;
        }
        
        .footer-info-content { color: #4b5563; font-size: 14px; line-height: 1.8; }
        
        .footer-contact-row {
            display: flex;
            gap: 20px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        
        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 13px;
        }
        
        .footer-contact-item.location::before { content: '📍'; font-size: 14px; }
        .footer-contact-item.phone::before { content: '📞'; font-size: 14px; }
        .footer-contact-item.email::before { content: '✉️'; font-size: 14px; }
        
        .footer-bottom {
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .footer-copyright { color: #9ca3af; font-size: 13px; line-height: 1.6; }
        
        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 968px) {
            .footer-content { flex-direction: column; gap: 25px; }
            .footer-logo-section { width: 100%; justify-content: center; }
            .footer-info { padding-left: 0; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
        
        @media (max-width: 768px) {
            .header-container { flex-direction: column; gap: 15px; padding: 15px 20px; }
            .container { padding: 0 15px; }
            .user-section { width: 100%; justify-content: space-between; }
            .header-text h1 { font-size: 22px; }
            .toast-container { top: 16px; right: 16px; width: calc(100vw - 32px); }
        }
    </style>
    @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- ============================================
         TOAST CONTAINER
    ============================================ -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- ============================================
         CUSTOM CONFIRM DIALOG
    ============================================ -->
    <div class="confirm-overlay" id="confirmOverlay">
        <div class="confirm-box">
            <div class="confirm-icon danger" id="confirmIcon">⚠️</div>
            <h3 id="confirmTitle">Konfirmasi</h3>
            <p id="confirmMessage">Apakah Anda yakin?</p>
            <div class="confirm-actions">
                <button class="btn btn-danger" id="confirmYesBtn" onclick="confirmAction()">Ya, Lanjutkan</button>
                <button class="btn" style="background:#f3f4f6;color:#374151;" onclick="closeConfirm()">Batal</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="header-container">
            <div class="header-logo">
                <img src="{{ asset('pictures/jabar.png') }}" alt="Logo Jabar" class="logo-jabar">
                <div class="header-text">
                    <h1>SETASET</h1>
                    <p>Sistem Inventaris Aset Diskominfo Jabar</p>
                </div>
            </div>
            
            <div class="user-section">
                @if(Auth::guard('stafaset')->user()->isAdmin())
                    <a href="{{ route('staff.index') }}" class="btn btn-warning btn-sm">
                        👥 Kelola Staff
                    </a>
                @endif
                
                <a href="{{ route('notifications.index') }}" class="notification-bell">
                    🔔
                    <span id="notif-count" class="notification-badge d-none"></span>
                </a>
                
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::guard('stafaset')->user()->nama, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ Auth::guard('stafaset')->user()->nama }}</span>
                        <span class="role-badge role-{{ Auth::guard('stafaset')->user()->role }}">
                            {{ Auth::guard('stafaset')->user()->role_label }}
                        </span>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container">
        @yield('content')
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-logo-section">
                    <img src="{{ asset('pictures/diskominfo.png') }}" alt="Diskominfo Logo">
                    <div class="footer-divider"></div>
                    <div class="footer-title-section">
                        <div class="footer-title">DISKOMINFO</div>
                        <div class="footer-subtitle">Provinsi Jawa Barat</div>
                    </div>
                </div>
                
                <div class="footer-info">
                    <div class="footer-info-title">
                        Dinas Komunikasi dan Informatika Provinsi Jawa Barat
                    </div>
                    <div class="footer-info-content">
                        <div class="footer-contact-item location">
                            Jl. Tamansari No.55, Bandung 40142
                        </div>
                        <div class="footer-contact-row">
                            <div class="footer-contact-item phone">(022) 7275127</div>
                            <div class="footer-contact-item email">info@diskominfo.jabarprov.go.id</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-copyright">
                    © {{ date('Y') }} Dinas Komunikasi dan Informatika Provinsi Jawa Barat.<br>
                    Hak Cipta Dilindungi Undang-Undang.
                </div>
            </div>
        </div>
    </div>
    
    @yield('scripts')
    
    <script>
    /* ============================================
       TOAST SYSTEM
    ============================================ */
    function showToast(type, title, message, duration = 4500) {
        const container = document.getElementById('toastContainer');
        
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const titles = {
            success: title || 'Berhasil!',
            error: title || 'Terjadi Kesalahan',
            warning: title || 'Perhatian',
            info: title || 'Informasi'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon-wrap">${icons[type]}</div>
            <div class="toast-body">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="dismissToast(this.parentElement)">✕</button>
        `;

        // Override progress duration
        toast.style.setProperty('--toast-duration', duration + 'ms');
        toast.querySelector('.toast-icon-wrap').parentElement;
        toast.style.cssText += ``;
        // Set progress bar duration via inline style on pseudo-element trick
        const style = document.createElement('style');
        const id = 'toast-' + Date.now();
        toast.id = id;
        style.textContent = `#${id}::after { animation-duration: ${duration}ms; }`;
        document.head.appendChild(style);

        container.appendChild(toast);

        const timer = setTimeout(() => dismissToast(toast), duration);
        toast._timer = timer;
        toast._style = style;

        return toast;
    }

    function dismissToast(toast) {
        if (!toast || toast.classList.contains('hiding')) return;
        clearTimeout(toast._timer);
        if (toast._style) toast._style.remove();
        toast.classList.add('hiding');
        setTimeout(() => toast.remove(), 300);
    }

    /* ============================================
       CUSTOM CONFIRM DIALOG
    ============================================ */
    let _confirmCallback = null;

    function showConfirm(options) {
        const { title, message, onConfirm, type = 'danger', confirmText = 'Ya, Lanjutkan' } = options;
        
        document.getElementById('confirmTitle').textContent = title || 'Konfirmasi';
        document.getElementById('confirmMessage').textContent = message || 'Apakah Anda yakin?';
        document.getElementById('confirmIcon').textContent = type === 'danger' ? '🗑️' : '⚠️';
        document.getElementById('confirmIcon').className = `confirm-icon ${type}`;
        document.getElementById('confirmYesBtn').textContent = confirmText;
        
        _confirmCallback = onConfirm;
        document.getElementById('confirmOverlay').classList.add('active');
    }

    function confirmAction() {
        closeConfirm();
        if (_confirmCallback) _confirmCallback();
    }

    function closeConfirm() {
        document.getElementById('confirmOverlay').classList.remove('active');
        _confirmCallback = null;
    }

    // Click outside to close confirm
    document.getElementById('confirmOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeConfirm();
    });

    /* ============================================
       INTERCEPT ALL FORM CONFIRMS
    ============================================ */
    document.addEventListener('DOMContentLoaded', function() {
        // Find all forms with onsubmit confirm
        document.querySelectorAll('form[onsubmit]').forEach(form => {
            const originalOnsubmit = form.getAttribute('onsubmit');
            if (originalOnsubmit && originalOnsubmit.includes('confirm(')) {
                // Extract the message from confirm('...')
                const match = originalOnsubmit.match(/confirm\(['"](.+?)['"]\)/);
                const message = match ? match[1] : 'Apakah Anda yakin?';
                
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');
                    showConfirm({
                        title: isDelete ? 'Hapus Data?' : 'Konfirmasi',
                        message: message,
                        type: isDelete ? 'danger' : 'warning',
                        confirmText: isDelete ? '🗑️ Ya, Hapus' : 'Ya, Lanjutkan',
                        onConfirm: () => {
                            form.removeEventListener('submit', arguments.callee);
                            form.submit();
                        }
                    });
                });
            }
        });

        // Show flash messages as toasts
        @if(session('success'))
            showToast('success', 'Berhasil!', @json(session('success')));
        @endif

        @if(session('error'))
            showToast('error', 'Gagal!', @json(session('error')));
        @endif

        @if($errors->any())
            const errMsgs = @json($errors->all());
            const errText = errMsgs.join('<br>');
            showToast('error', 'Validasi Gagal', errText, 6000);
        @endif
    });

    /* ============================================
       NOTIFICATION REALTIME
    ============================================ */
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
    }, 5000);
    </script>
</body>
</html>