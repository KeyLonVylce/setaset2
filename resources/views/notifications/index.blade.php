@extends('layouts.app')

@section('title', 'Notifikasi - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/notifications/index.css') }}">
@endsection

@section('content')
<div class="notif-container">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> &nbsp; /&nbsp; 
        <span>&nbsp;Notifikasi</span>
    </div>

    <div class="filter-section" style="background: linear-gradient(135deg, #0066cc 0%, #004c99 100%); color:#fff;">
        <center><h5>Notifikasi Aktivitas</h5></center>
    </div>

    {{-- FILTER (tetap menggunakan GET, tanpa parameter limit) --}}
    <div class="filter-section">
        <h5>Filter</h5>
        <form method="GET" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status')=='all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="unread" {{ request('status')=='unread' ? 'selected' : '' }}>Belum Dibaca</option>
                        <option value="read" {{ request('status')=='read' ? 'selected' : '' }}>Sudah Dibaca</option>
                    </select>
                </div>

                <!-- Filter jenis notifikasi (hanya untuk admin) -->
                @if($user && $user->role === 'admin')
                <div class="filter-group">
                    <select name="type" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ request('type')=='all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="barang" {{ request('type')=='barang' ? 'selected' : '' }}>Barang</option>
                        <option value="lantai" {{ request('type')=='lantai' ? 'selected' : '' }}>Lantai</option>
                        <option value="ruangan" {{ request('type')=='ruangan' ? 'selected' : '' }}>Ruangan</option>
                    </select>
                </div>
                @endif
            </div>
        </form>
    </div>

    <div class="notif-list-wrapper">
        <h5 class="notif-list-title">Notifikasi</h5>

        {{-- Tempat daftar notifikasi akan di-render oleh JS --}}
        <div id="notifications-list"></div>

        {{-- Tempat tombol aksi --}}
        <div id="action-buttons-container" class="load-more-actions"></div>
    </div>
</div>

<script>
// Data semua notifikasi dari server (diencode ke JSON)
const allNotifications = @json($notifications);
const userId = {{ $user->id }};

// Fungsi untuk mengecek apakah suatu notifikasi sudah dibaca oleh user
function isReadBy(notif, userId) {
    if (!notif.read_by) return false;
    return notif.read_by.includes(userId);
}

// Mapping aksi ke emoji (5 aksi utama)
function getActionEmoji(aksi) {
    const a = (aksi || '').toLowerCase();
    const emojiMap = {
        'pindah': '🔄',
        'import': '📥',
        'tambah': '➕',
        'edit': '✏️',
        'hapus': '🗑️',
        'delete': '🗑️',
        'add': '➕',
        'update': '✏️'
    };
    return emojiMap[a] || '📢'; // default emoji
}

// Render notifikasi dengan emoji di samping pesan
function renderNotifications(limit) {
    const container = document.getElementById('notifications-list');
    if (!container) return;

    const itemsToShow = allNotifications.slice(0, limit);
    if (itemsToShow.length === 0) {
        container.innerHTML = `
            <div class="empty-notif">
                <i class="bi bi-bell-slash"></i>
                <strong>Tidak ada Notifikasi</strong>
                <p>Belum ada aktivitas yang tercatat.</p>
            </div>
        `;
        return;
    }

    let html = '';
    itemsToShow.forEach(notif => {
        const isRead = isReadBy(notif, userId);
        const actionEmoji = getActionEmoji(notif.aksi);
        // Gabungkan emoji dengan teks pesan
        const messageWithEmoji = `${notif.pesan}`;
        
        html += `
            <div class="notif-item ${isRead ? 'read' : 'unread'}">
                <div class="notif-content">
                    <div class="notif-meta">
                        <span class="notif-date">
                            <i class="bi bi-clock"></i> ${notif.created_at_human || notif.created_at}
                        </span>
                        <span class="badge-type text-capitalize">${notif.type}</span>
                        <span class="badge-aksi text-capitalize">${notif.aksi}</span>
                    </div>
                    <div class="notif-message">
                        ${messageWithEmoji}
                    </div>
                </div>
                ${!isRead ? `
                <form action="/notifications/read/${notif.id}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button class="btn-mark-read" title="Tandai dibaca">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
                ` : ''}
            </div>
        `;
    });
    container.innerHTML = html;
}

// Fungsi untuk mengupdate tombol aksi berdasarkan jumlah yang sedang tampil
function updateButtons(currentLimit, total) {
    const container = document.getElementById('action-buttons-container');
    if (!container) return;

    // Jika total notifikasi <= 10, tidak perlu tombol apapun
    if (total <= 10) {
        container.innerHTML = '';
        return;
    }

    // Jika semua notifikasi sudah tampil
    if (currentLimit >= total) {
        container.innerHTML = `
            <button type="button" id="btnShowLess" class="btn-show-less">
                <i class="bi bi-arrow-up-circle"></i> Muat Lebih Sedikit
            </button>
        `;
        const btnShowLess = document.getElementById('btnShowLess');
        if (btnShowLess) btnShowLess.onclick = () => {
            renderNotifications(10);
            updateButtons(10, total);
        };
        return;
    }

    // Belum semua tampil
    container.innerHTML = `
        <button type="button" id="btnLoadMore" class="btn-load-more">
            <i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak
        </button>
        <button type="button" id="btnShowAll" class="btn-show-all">
            <i class="bi bi-window-stack"></i> Tampilkan Semua
        </button>
    `;

    const btnLoadMore = document.getElementById('btnLoadMore');
    const btnShowAll = document.getElementById('btnShowAll');

    if (btnLoadMore) {
        btnLoadMore.onclick = () => {
            let newLimit = currentLimit + 10;
            if (newLimit > total) newLimit = total;
            renderNotifications(newLimit);
            updateButtons(newLimit, total);
        };
    }

    if (btnShowAll) {
        btnShowAll.onclick = () => {
            renderNotifications(total);
            updateButtons(total, total);
        };
    }
}

// Inisialisasi pertama kali
const totalNotif = allNotifications.length;
let currentLimit = Math.min(10, totalNotif);
renderNotifications(currentLimit);
updateButtons(currentLimit, totalNotif);
</script>
@endsection