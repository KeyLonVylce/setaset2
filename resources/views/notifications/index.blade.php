@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card shadow-sm">

        {{-- HEADER --}}
        <div class="card-header bg-white d-flex align-items-center gap-2">
            <a href="{{ route('home') }}"
               class="btn btn-sm btn-outline-secondary px-2 py-1"
               title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>

            <h5 class="mb-0">📢 Notifikasi Aktivitas</h5>
        </div>

        {{-- FILTER --}}
        <div class="card-body border-bottom">
            <div class="row g-2">

                {{-- STATUS --}}
                <div class="col-md-4">
                    <select id="filterStatus" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="unread">Belum Dibaca</option>
                        <option value="read">Sudah Dibaca</option>
                    </select>
                </div>

                {{-- TYPE (ADMIN ONLY) --}}
                @if($user->role === 'admin')
                <div class="col-md-4">
                    <select id="filterType" class="form-select">
                        <option value="all">Semua Kategori</option>
                        <option value="barang">Barang</option>
                        <option value="lantai">Lantai</option>
                        <option value="ruangan">Ruangan</option>
                    </select>
                </div>
                @endif

            </div>
        </div>

        {{-- LIST --}}
        <ul class="list-group list-group-flush" id="notifList">
            @forelse($notifications as $notif)
            @php
                $isRead = $notif->isReadBy($user->id);
            @endphp

            <li class="list-group-item notif-item
                {{ $isRead ? 'read' : 'unread bg-light fw-semibold' }}"
                data-status="{{ $isRead ? 'read' : 'unread' }}"
                data-type="{{ strtolower(trim($notif->type)) }}"
            >

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="d-flex gap-2 align-items-center">
                            <small class="text-muted">
                                {{ $notif->created_at->diffForHumans() }}
                            </small>

                            <span class="badge bg-secondary text-capitalize">
                                {{ $notif->type }}
                            </span>

                            <span class="badge bg-info text-capitalize">
                                {{ $notif->aksi }}
                            </span>
                        </div>

                        <div class="mt-1">
                            {!! $notif->pesan !!}
                        </div>
                    </div>

                    {{-- CEKLIS --}}
                    @if(!$isRead)
                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-success rounded-circle"
                                title="Tandai dibaca">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                    @endif

                </div>
            </li>

            @empty
            <li class="list-group-item text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                <strong>Tidak ada Notifikasi</strong>
            </li>
            @endforelse
        </ul>

        {{-- EMPTY FILTER STATE --}}
        <div id="emptyFilter"
             class="text-center text-muted py-5 d-none">
            <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
            <strong>Tidak ada Notifikasi</strong>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white">
            {{ $notifications->links() }}
        </div>

    </div>
</div>

{{-- FILTER SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('filterStatus');
    const typeFilter   = document.getElementById('filterType');
    const items        = document.querySelectorAll('.notif-item');
    const emptyState   = document.getElementById('emptyFilter');

    function filterNotif() {
        const status = statusFilter.value;
        const type   = typeFilter ? typeFilter.value.toLowerCase() : 'all';

        let visible = 0;

        items.forEach(item => {
            const itemStatus = item.dataset.status;
            const itemType   = item.dataset.type;

            let show = true;

            if (status !== 'all' && itemStatus !== status) {
                show = false;
            }

            if (type !== 'all' && itemType !== type) {
                show = false;
            }

            item.style.display = show ? 'block' : 'none';

            if (show) visible++;
        });

        emptyState.classList.toggle('d-none', visible > 0);
    }

    statusFilter.addEventListener('change', filterNotif);
    if (typeFilter) {
        typeFilter.addEventListener('change', filterNotif);
    }
});
</script>
@endsection
