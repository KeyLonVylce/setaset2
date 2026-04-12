@extends('layouts.app')

@section('title', $lantai->nama_lantai . ' - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/lantai/show.css') }}">
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;{{ $lantai->nama_lantai }}
</div>

<div class="card">
    <div class="page-header">
        <h2>{{ $lantai->nama_lantai }}</h2>
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Cari ruangan..." value="{{ request('search') }}">
            </form>
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <button class="btn btn-primary" onclick="openAddRuanganModal()">+ Tambah Ruangan</button>
            @endif
        </div>
    </div>

    @if($lantai->keterangan || $ruangans->total() > 0)
    <div class="lantai-info">
        @if($lantai->keterangan)
        <p><strong>Keterangan:</strong> {{ $lantai->keterangan }}</p>
        @endif
        <p><strong>Total Ruangan:</strong> {{ $ruangans->total() }}</p>
    </div>
    @endif

    @if($ruangans->count() > 0)
    <div class="ruangan-grid">
        @foreach($ruangans as $ruangan)
        <div class="ruangan-card-wrapper">
            @if(Auth::guard('stafaset')->user()->isAdmin())
            <div class="ruangan-card-actions">
                <button onclick="event.preventDefault(); openEditRuanganModal(
                    {{ $ruangan->id }},
                    '{{ addslashes($ruangan->nama_ruangan) }}',
                    '{{ addslashes($ruangan->keterangan ?? '') }}'
                )" title="Edit Ruangan">✏️</button>

                <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-delete" title="Hapus Ruangan"
                        onclick="var f=this.closest('form'); showConfirm({
                            title: 'Hapus Ruangan?',
                            message: 'Yakin ingin menghapus ruangan <strong>{{ addslashes($ruangan->nama_ruangan) }}</strong>?<br>Semua barang di dalamnya akan ikut terhapus.',
                            type: 'danger',
                            confirmText: '🗑️ Ya, Hapus',
                            onConfirm: function() { f.submit(); }
                        })">🗑️</button>
                </form>
            </div>
            @endif
            <a href="{{ route('ruangan.show', $ruangan->id) }}" class="ruangan-card">
                <h3>{{ $ruangan->nama_ruangan }}</h3>
                <div>
                    <span class="badge">{{ $ruangan->barangs_count }} Barang</span>
                </div>
                @if($ruangan->keterangan)
                <p>{{ Str::limit($ruangan->keterangan, 80) }}</p>
                @endif
            </a>
        </div>
        @endforeach
    </div>

    @if($ruangans->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $ruangans->firstItem() }} sampai {{ $ruangans->lastItem() }} dari {{ $ruangans->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                @if ($ruangans->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $ruangans->previousPageUrl() }}" rel="prev">‹</a></li>
                @endif

                @foreach(range(1, $ruangans->lastPage()) as $page)
                    @if ($page == $ruangans->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $ruangans->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($ruangans->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $ruangans->nextPageUrl() }}" rel="next">›</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">›</span></li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <h3>Tidak Ada Ruangan</h3>
        <p>{{ request('search') ? 'Pencarian tidak ditemukan.' : 'Klik tombol "Tambah Ruangan" untuk memulai.' }}</p>
    </div>
    @endif
</div>

@if(Auth::guard('stafaset')->user()->isAdmin())
<!-- Modal Tambah Ruangan -->
<div id="addRuanganModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Ruangan Baru</h3>
            <span class="close" onclick="closeAddRuanganModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="addRuanganForm" action="{{ route('ruangan.store', $lantai->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama_ruangan">Nama Ruangan <span style="color: red;">*</span></label>
                    <input type="text" id="nama_ruangan" name="nama_ruangan" placeholder="Contoh: Ruang Server" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="addRuanganForm" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" onclick="closeAddRuanganModal()">Batal</button>
        </div>
    </div>
</div>

<!-- Modal Edit Ruangan -->
<div id="editRuanganModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Ruangan</h3>
            <span class="close" onclick="closeEditRuanganModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editRuanganForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit_nama_ruangan">Nama Ruangan <span style="color: red;">*</span></label>
                    <input type="text" id="edit_nama_ruangan" name="nama_ruangan" required>
                </div>
                <div class="form-group">
                    <label for="edit_keterangan">Keterangan</label>
                    <textarea id="edit_keterangan" name="keterangan" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="editRuanganForm" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditRuanganModal()">Batal</button>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function openAddRuanganModal() {
        document.getElementById('addRuanganModal').style.display = 'flex';
    }
    function closeAddRuanganModal() {
        document.getElementById('addRuanganModal').style.display = 'none';
    }

    function openEditRuanganModal(id, nama, keterangan) {
        document.getElementById('editRuanganForm').action = '/admin/ruangan/' + id;
        document.getElementById('edit_nama_ruangan').value = nama;
        document.getElementById('edit_keterangan').value = keterangan || '';
        document.getElementById('editRuanganModal').style.display = 'flex';
    }

    function closeEditRuanganModal() {
        document.getElementById('editRuanganModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const addModal = document.getElementById('addRuanganModal');
        const editModal = document.getElementById('editRuanganModal');
        if (event.target === addModal) addModal.style.display = 'none';
        if (event.target === editModal) editModal.style.display = 'none';
    }
</script>
@endsection