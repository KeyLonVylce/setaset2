@extends('layouts.app')

@section('title', 'Notifikasi - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/notifications/index.css') }}">
@endsection

@section('content')
<div class="notif-container">

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <span>&nbsp;Notifikasi</span>
</div>

    <div class="filter-section" style="background: linear-gradient(135deg, #0066cc 0%, #004c99 100%); color:#fff;">
        <center><h5>📢 Notifikasi Aktivitas</h5></center>
    </div>

    {{-- FILTER (SERVER SIDE) --}}
    <div class="filter-section">
        <h5>Filter</h5>
        <form method="GET">
            <div class="filter-row">
                <div class="filter-group">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all">Status</option>
                        <option value="unread" {{ request('status')=='unread'?'selected':'' }}>Belum Dibaca</option>
                        <option value="read" {{ request('status')=='read'?'selected':'' }}>Sudah Dibaca</option>
                    </select>
                </div>

                @if($user && $user->role === 'admin')
                <div class="filter-group">
                    <select name="type" class="filter-select" onchange="this.form.submit()">
                        <option value="all">Kategori</option>
                        <option value="barang" {{ request('type')=='barang'?'selected':'' }}>Barang</option>
                        <option value="lantai" {{ request('type')=='lantai'?'selected':'' }}>Lantai</option>
                        <option value="ruangan" {{ request('type')=='ruangan'?'selected':'' }}>Ruangan</option>
                    </select>
                </div>
                @endif
            </div>
        </form>
    </div>

    <div class="notif-list-wrapper">
    <h5 class="notif-list-title">Notifikasi</h5>
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

</div>
@endsection