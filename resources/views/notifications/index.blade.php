@extends('layouts.app')

@section('content')
<div class="container">
    <h4>📢 Notifikasi Aktivitas</h4>

    <ul class="list-group mt-3">
        @forelse($notifications as $notif)
            <li class="list-group-item">
                <small class="text-muted">
                    {{ $notif->created_at->diffForHumans() }}
                </small>
                <br>
                {!! $notif->pesan !!}
            </li>
        @empty
            <li class="list-group-item text-center text-muted">
                Belum ada notifikasi
            </li>
        @endforelse
    </ul>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
