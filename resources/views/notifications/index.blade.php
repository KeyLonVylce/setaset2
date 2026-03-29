@extends('layouts.app')

@section('title', 'Notifikasi - SETASET')

@section('styles')
<style>
.notif-container {
    max-width: 1000px;
    margin: 0 auto;
}

/* HEADER */
.notif-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.notif-header h5 {
    font-size: 24px;
    font-weight: 700;
    color: #0066cc;
    margin: 0;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f3f4f6;
    border-radius: 8px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}
.btn-back:hover {
    background: #e5e7eb;
    transform: translateX(-2px);
    text-decoration: none;
    color: #374151;
}

/* FILTER SECTION */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.filter-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.filter-group {
    flex: 1;
    min-width: 200px;
}
.filter-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.filter-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}
.filter-select:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
}

/* NOTIF LIST */
.notif-list {
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.notif-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
    background: white;
}
.notif-item.unread {
    border-left: 4px solid #0066cc;
}
.notif-item.read {
    border-left: 4px solid transparent;
}
.notif-item:hover {
    background: #f0f7ff;  /* biru lembut saat hover */
}
.notif-content {
    flex: 1;
    padding-right: 20px;
}
.notif-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}
.notif-date {
    font-size: 12px;
    color: #6b7280;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-type, .badge-aksi {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.badge-type {
    background: #eef2ff;
    color: #1e40af;
}
.badge-aksi {
    background: #f3f4f6;
    color: #374151;
}
.notif-message {
    font-size: 14px;
    color: #1f2937;
    line-height: 1.5;
    margin: 8px 0 0 0;
}
.notif-message strong {
    color: #0066cc;
}

/* TOMBOL CENTANG (TENGAH VERTIKAL) */
.btn-mark-read {
    background: #f3f4f6;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.btn-mark-read:hover {
    background: #0066cc;
    color: white;
    transform: scale(1.05);
}

/* EMPTY STATE */
.empty-notif {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
}
.empty-notif i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
    display: inline-block;
}
.empty-notif strong {
    font-size: 18px;
    color: #374151;
    display: block;
    margin-bottom: 8px;
}
.empty-notif p {
    color: #6b7280;
    font-size: 14px;
}

/* PAGINATION */
.pagination-wrapper {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}
.pagination {
    display: flex;
    list-style: none;
    gap: 8px;
    padding: 0;
    margin: 0;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
}
.page-item {
    display: inline-block;
}
.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s;
    background: white;
    font-size: 14px;
    font-weight: 500;
}
.page-link:hover {
    background: #f3f4f6;
    border-color: #0066cc;
    color: #0066cc;
}
.page-item.active .page-link {
    background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
    color: white;
    border-color: #0066cc;
    font-weight: 600;
}
.page-item.disabled .page-link {
    color: #d1d5db;
    cursor: not-allowed;
    background: #f9fafb;
    border-color: #e5e7eb;
}
.page-item.disabled .page-link:hover {
    background: #f9fafb;
    border-color: #e5e7eb;
    color: #d1d5db;
}

/* RESPONSIVE */
@media (max-width: 640px) {
    .notif-item {
        padding: 16px;
        flex-direction: column;
        align-items: flex-start;
    }
    .notif-content {
        padding-right: 0;
        margin-bottom: 12px;
    }
    .btn-mark-read {
        align-self: flex-start;
    }
    .notif-meta {
        gap: 8px;
    }
    .page-link {
        min-width: 32px;
        height: 32px;
        font-size: 13px;
    }
}
</style>
@endsection

@section('content')
<div class="notif-container">

    {{-- HEADER --}}
    <div class="notif-header">
        <a href="{{ route('home') }}" class="btn-back">
            ← Kembali
        </a>
    </div>

    <div class="filter-section" style="background: linear-gradient(135deg, #0066cc 0%, #004c99 100%); color:#fff;">
        <center><h5>📢 Notifikasi Aktivitas</h5></center>
    </div>

    {{-- FILTER (SERVER SIDE) --}}
    <div class="filter-section">
        <form method="GET">
            <div class="filter-row">
                <div class="filter-group">
                    <label>STATUS</label>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all">Semua</option>
                        <option value="unread" {{ request('status')=='unread'?'selected':'' }}>Belum Dibaca</option>
                        <option value="read" {{ request('status')=='read'?'selected':'' }}>Sudah Dibaca</option>
                    </select>
                </div>

                @if($user && $user->role === 'admin')
                <div class="filter-group">
                    <label>KATEGORI</label>
                    <select name="type" class="filter-select" onchange="this.form.submit()">
                        <option value="all">Semua</option>
                        <option value="barang" {{ request('type')=='barang'?'selected':'' }}>Barang</option>
                        <option value="lantai" {{ request('type')=='lantai'?'selected':'' }}>Lantai</option>
                        <option value="ruangan" {{ request('type')=='ruangan'?'selected':'' }}>Ruangan</option>
                    </select>
                </div>
                @endif
            </div>
        </form>
    </div>

    {{-- LIST NOTIFIKASI --}}
    @forelse($notifications as $notif)
        @php
            $isRead = $notif->isReadBy($user->id);
            $type = strtolower(trim($notif->type ?? 'lainnya'));
        @endphp

        <div class="notif-item {{ $isRead ? 'read' : 'unread' }}">
            <div class="notif-content">
                <div class="notif-meta">
                    <span class="notif-date">
                        <i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}
                    </span>
                    <span class="badge-type text-capitalize">{{ $notif->type }}</span>
                    <span class="badge-aksi text-capitalize">{{ $notif->aksi }}</span>
                </div>
                <div class="notif-message">
                    {!! $notif->pesan !!}
                </div>
            </div>

            @if(!$isRead)
            <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                @csrf
                <button class="btn-mark-read" title="Tandai dibaca">
                    <i class="bi bi-check-lg"></i>
                </button>
            </form>
            @endif
        </div>

    @empty
        <div class="empty-notif">
            <i class="bi bi-bell-slash"></i>
            <strong>Tidak ada Notifikasi</strong>
            <p>Belum ada aktivitas yang tercatat.</p>
        </div>
    @endforelse

    {{-- PAGINATION --}}
    @if($notifications->hasPages())
    <div class="pagination-wrapper">
        {{ $notifications->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
    @endif

</div>
@endsection