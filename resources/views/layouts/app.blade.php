<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SETASET - Inventaris Diskominfo')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Custom Confirm Dialog -->
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
                <a href="{{ route('staff.index') }}" class="staff-button">
                    <span class="staff-icon">👥</span>
                    <span class="staff-text">Kelola Staff</span>
                </a>
            @endif
                
                <a href="{{ route('notifications.index') }}" class="notification-bell">
                    🔔
                    <span id="notif-count" class="notification-badge d-none"></span>
                </a>
                
                <a href="{{ route('profile.edit') }}" class="user-info-link">
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
                </a>
                
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

// 🔥 Overlay (background gelap)
let overlay = document.getElementById('customPopupOverlay');

if (!overlay) {
    overlay = document.createElement('div');
    overlay.id = 'customPopupOverlay';
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.background = 'rgba(0,0,0,0.5)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.zIndex = '99999';
    document.body.appendChild(overlay);
}

// 🔥 Box popup
const popup = document.createElement('div');
popup.style.background = '#fff';
popup.style.padding = '20px 25px';
popup.style.borderRadius = '10px';
popup.style.textAlign = 'center';
popup.style.minWidth = '300px';
popup.style.boxShadow = '0 5px 15px rgba(0,0,0,0.3)';
popup.style.animation = 'fadeIn 0.3s ease';

// warna icon
let color = '#d33';
if (type === 'success') color = '#28a745';
if (type === 'warning') color = '#f39c12';

popup.innerHTML = `
    <div style="font-size:40px; margin-bottom:10px; color:${color};">
        ${type === 'error' ? '❌' : type === 'success' ? '✅' : '⚠️'}
    </div>
    <h3 style="margin-bottom:10px;">${title}</h3>
    <p style="margin-bottom:20px;">${message}</p>
    <button id="popupOkBtn" style="
        background:${color};
        color:#fff;
        border:none;
        padding:8px 20px;
        border-radius:5px;
        cursor:pointer;
    ">OK</button>
`;

overlay.innerHTML = '';
overlay.appendChild(popup);

// tombol OK
document.getElementById('popupOkBtn').onclick = function () {
    overlay.remove();
};
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
    const { 
        title, 
        message, 
        onConfirm, 
        type = 'danger', 
        confirmText = 'Ya, Lanjutkan', 
        showConfirmOnly = false 
    } = options;
    
    document.getElementById('confirmTitle').textContent = title || 'Konfirmasi';
    document.getElementById('confirmMessage').innerHTML = message || 'Apakah Anda yakin?';
    document.getElementById('confirmIcon').textContent = type === 'danger' ? '🗑️' : '⚠️';
    document.getElementById('confirmIcon').className = `confirm-icon ${type}`;
    document.getElementById('confirmYesBtn').textContent = confirmText;
    
    const cancelBtn = document.querySelector('.confirm-actions .btn:last-child');

    if (showConfirmOnly) {
        cancelBtn.style.display = 'none';
        document.getElementById('confirmYesBtn').className = 'btn btn-warning';
    } else {
        cancelBtn.style.display = '';
        document.getElementById('confirmYesBtn').className = 'btn btn-danger';
    }
    
    _confirmCallback = onConfirm;
    document.getElementById('confirmOverlay').classList.add('active');
}

function confirmAction() {
    var cb = _confirmCallback;  
    closeConfirm();
    if (cb) cb();
}

    function closeConfirm() {
        document.getElementById('confirmOverlay').classList.remove('active');
        _confirmCallback = null;
    }

    const confirmOverlay = document.getElementById('confirmOverlay');

    if (confirmOverlay) {
        confirmOverlay.addEventListener('click', function(e) {
            if (e.target === this) closeConfirm();
        });
    }

    /* ============================================
       INTERCEPT FORM CONFIRMS — FIXED
    ============================================ */
    window.onload = function () {

    @if(session('error'))
        showToast('error', 'Akses Ditolak', @json(session('error')));
    @endif

};

    /* ============================================
       NOTIFICATION REALTIME
    ============================================ */
    setInterval(function() {
        fetch('{{ route('notifications.realtime') }}')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var badge = document.getElementById('notif-count');
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