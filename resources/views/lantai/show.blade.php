@extends('layouts.app')

@section('title', $lantai->nama_lantai . ' - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
    .page-header h2 { font-size: 28px; color: #333; margin: 0; }

    /* Search Box */
    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .search-box form {
        margin: 0;
    }
    .search-box input {
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 250px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .search-box input:focus {
        outline: none;
        border-color: #ff7b3d;
    }

    .lantai-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .lantai-info p { margin: 5px 0; color: #666; }
    .ruangan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .ruangan-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s; }
    .ruangan-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(255,123,61,0.3); }
    .ruangan-card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
    .ruangan-card h3 { font-size: 18px; color: #ff7b3d; margin-bottom: 5px; }
    .ruangan-card p { color: #666; font-size: 14px; margin: 5px 0; }
    .ruangan-actions { display: flex; gap: 10px; margin-top: 15px; }
    .badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; }
    .badge-info { background: #d1ecf1; color: #0c5460; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .action-buttons { display: flex; gap: 5px; }
    .btn-icon { background: none; border: none; cursor: pointer; font-size: 18px; padding: 5px; color: #666; transition: color 0.3s; }
    .btn-icon:hover { color: #ff7b3d; }
    .btn-icon.delete:hover { color: #dc3545; }

    /* Modal */
    .modal { display: none; position: fixed; z-index: 1000; padding-top: 100px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background: rgba(0,0,0,0.4); }
    .modal-content { background: #fff; padding: 20px; border-radius: 10px; width: 400px; margin: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { margin: 0; }
    .close { cursor: pointer; font-size: 24px; color: #999; }
    .close:hover { color: #333; }
    .form-group { margin-bottom: 15px; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }

    /* Pagination Styles */
    .pagination-wrapper { 
        display: flex; 
        justify-content: space-between;
        align-items: center;
        margin-top: 30px; 
        padding: 20px 0;
        flex-wrap: wrap;
        gap: 15px;
    }
    .pagination-info {
        color: #666;
        font-size: 14px;
    }
    .pagination-nav {
        display: flex;
    }
    .pagination { 
        display: flex; 
        list-style: none; 
        gap: 5px; 
        padding: 0; 
        margin: 0; 
        align-items: center;
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
        border: 1px solid #ddd; 
        border-radius: 50%;
        color: #666; 
        text-decoration: none; 
        transition: all 0.2s;
        background: white;
        font-size: 14px;
        cursor: pointer;
    }
    .page-link:hover { 
        background: #f5f5f5; 
        border-color: #bbb; 
    }
    .page-item.active .page-link { 
        background: #00a8ff; 
        color: white; 
        border-color: #00a8ff; 
        font-weight: 600;
        cursor: default;
    }
    .page-item.disabled .page-link { 
        color: #ccc; 
        cursor: not-allowed; 
        background: #fafafa;
        border-color: #e5e5e5;
    }
    .page-item.disabled .page-link:hover { 
        background: #fafafa; 
        border-color: #e5e5e5; 
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            justify-content: center;
        }
        .pagination-info {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / {{ $lantai->nama_lantai }}
</div>

<div class="card">
    <!-- HEADER + SEARCH -->
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
        <div class="ruangan-card">
            <div class="ruangan-card-header">
                <div>
                    <h3>{{ $ruangan->nama_ruangan }}</h3>
                    <span class="badge badge-info">{{ $ruangan->barangs_count }} Barang</span>
                </div>
                @if(Auth::guard('stafaset')->user()->isAdmin())
                <div class="action-buttons">
                    <button class="btn-icon" onclick="openEditRuanganModal({{ $ruangan->id }}, '{{ addslashes($ruangan->nama_ruangan) }}', '{{ addslashes($ruangan->penanggung_jawab ?? '') }}', '{{ addslashes($ruangan->nip_penanggung_jawab ?? '') }}', '{{ addslashes($ruangan->keterangan ?? '') }}')" title="Edit Ruangan">✏️</button>
                    <form action="{{ route('ruangan.delete', $ruangan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus ruangan ini? Semua barang di dalamnya akan ikut terhapus!')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon delete" title="Hapus Ruangan">🗑️</button>
                    </form>
                </div>
                @endif
            </div>
            
            @if($ruangan->penanggung_jawab)
            <p><strong>Penanggung Jawab:</strong> {{ $ruangan->penanggung_jawab }}</p>
            @endif
            
            @if($ruangan->nip_penanggung_jawab)
            <p><strong>NIP:</strong> {{ $ruangan->nip_penanggung_jawab }}</p>
            @endif
            
            @if($ruangan->keterangan)
            <p><strong>Keterangan:</strong> {{ Str::limit($ruangan->keterangan, 100) }}</p>
            @endif

            <div class="ruangan-actions">
                <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($ruangans->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $ruangans->firstItem() }} sampai {{ $ruangans->lastItem() }} dari {{ $ruangans->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($ruangans->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $ruangans->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach(range(1, $ruangans->lastPage()) as $page)
                    @if ($page == $ruangans->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $ruangans->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($ruangans->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $ruangans->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>
                @endif

                {{-- Last Page Link --}}
                @if ($ruangans->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $ruangans->url($ruangans->lastPage()) }}">»</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
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
        <form action="{{ route('ruangan.store', $lantai->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_ruangan">Nama Ruangan <span style="color: red;">*</span></label>
                <input type="text" id="nama_ruangan" name="nama_ruangan" required>
            </div>
            <div class="form-group">
                <label for="penanggung_jawab">Penanggung Jawab</label>
                <input type="text" id="penanggung_jawab" name="penanggung_jawab">
            </div>
            <div class="form-group">
                <label for="nip_penanggung_jawab">NIP Penanggung Jawab</label>
                <input type="text" id="nip_penanggung_jawab" name="nip_penanggung_jawab">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Ruangan -->
<div id="editRuanganModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Ruangan</h3>
            <span class="close" onclick="closeEditRuanganModal()">&times;</span>
        </div>
        <form id="editRuanganForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_nama_ruangan">Nama Ruangan <span style="color: red;">*</span></label>
                <input type="text" id="edit_nama_ruangan" name="nama_ruangan" required>
            </div>
            <div class="form-group">
                <label for="edit_penanggung_jawab">Penanggung Jawab</label>
                <input type="text" id="edit_penanggung_jawab" name="penanggung_jawab">
            </div>
            <div class="form-group">
                <label for="edit_nip_penanggung_jawab">NIP Penanggung Jawab</label>
                <input type="text" id="edit_nip_penanggung_jawab" name="nip_penanggung_jawab">
            </div>
            <div class="form-group">
                <label for="edit_keterangan">Keterangan</label>
                <textarea id="edit_keterangan" name="keterangan"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function openAddRuanganModal() { 
        document.getElementById('addRuanganModal').style.display = 'block'; 
    }
    
    function closeAddRuanganModal() { 
        document.getElementById('addRuanganModal').style.display = 'none'; 
    }
    
    function openEditRuanganModal(id, nama, penanggungJawab, nip, keterangan) {
        document.getElementById('editRuanganForm').action = '/ruangan/' + id;
        document.getElementById('edit_nama_ruangan').value = nama;
        document.getElementById('edit_penanggung_jawab').value = penanggungJawab || '';
        document.getElementById('edit_nip_penanggung_jawab').value = nip || '';
        document.getElementById('edit_keterangan').value = keterangan || '';
        document.getElementById('editRuanganModal').style.display = 'block';
    }
    
    function closeEditRuanganModal() {
        document.getElementById('editRuanganModal').style.display = 'none';
    }
    
    window.onclick = function(event) { 
        const addModal = document.getElementById('addRuanganModal'); 
        const editModal = document.getElementById('editRuanganModal');
        if (event.target == addModal) { 
            addModal.style.display = 'none'; 
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
    }
</script>
@endsection