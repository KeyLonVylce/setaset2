@extends('layouts.app')

@section('title', 'Home - SETASET')

@section('styles')
<style>
    .welcome-card { text-align: center; padding: 40px; background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); color: white; border-radius: 10px; margin-bottom: 30px; }
    .welcome-card h2 { font-size: 28px; margin-bottom: 10px; }
    .lantai-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .lantai-header h3 { font-size: 24px; color: #333; }
    .lantai-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
    .lantai-card-wrapper { position: relative; }
    .lantai-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; color: #333; display: block; }
    .lantai-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(255,123,61,0.3); }
    .lantai-card h4 { font-size: 20px; margin-bottom: 10px; color: #ff7b3d; }
    .lantai-card p { color: #666; font-size: 14px; }
    .lantai-card .badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; background: #d1ecf1; color: #0c5460; margin: 5px; }
    .lantai-card-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 5px; z-index: 10; }
    .lantai-card-actions button { background: none; border: none; cursor: pointer; font-size: 18px; padding: 5px; color: #999; transition: color 0.3s; }
    .lantai-card-actions button:hover { color: #ff7b3d; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    
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
<div class="welcome-card">
    <h2>Selamat Datang di SETASET</h2>
    <p>Sistem Inventaris Barang Diskominfo</p>
</div>

<div class="card">
    <div class="lantai-header">
        <h3>Daftar Lantai</h3>
        @if(Auth::guard('stafaset')->user()->isAdmin())
            <button class="btn btn-primary" onclick="openAddLantaiModal()">+ Tambah Lantai</button>
        @endif
    </div>

    <div class="pemindahan">
        <div class="pemindahan-header">
            <a href="{{ route('pemindahan.pindah') }}" class="btn btn-primary">
                Pindahkan Barang
            </a>
        </div>
    </div>

    @if($lantais->count() > 0)
    <div class="lantai-grid">
        @foreach($lantais as $lantai)
        <div class="lantai-card-wrapper">
            @if(Auth::guard('stafaset')->user()->isAdmin())
            <div class="lantai-card-actions">
                <button onclick="event.preventDefault(); openEditLantaiModal({{ $lantai->id }}, '{{ addslashes($lantai->nama_lantai) }}', {{ $lantai->urutan }}, '{{ addslashes($lantai->keterangan ?? '') }}')" title="Edit">✏️</button>
                <form action="{{ route('lantai.destroy', $lantai->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus lantai ini? Semua ruangan dan barang akan ikut terhapus!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus">🗑️</button>
                </form>
            </div>
            @endif
            <a href="{{ route('lantai.show', $lantai->id) }}" class="lantai-card">
                <h4>{{ $lantai->nama_lantai }}</h4>
                <div>
                    <span class="badge">{{ $lantai->ruangans_count }} Ruangan</span>
                </div>
                @if($lantai->keterangan)
                <p style="margin-top: 10px; font-size: 12px;">{{ Str::limit($lantai->keterangan, 50) }}</p>
                @endif
            </a>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($lantais->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $lantais->firstItem() }} sampai {{ $lantais->lastItem() }} dari {{ $lantais->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($lantais->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $lantais->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach(range(1, $lantais->lastPage()) as $page)
                    @if ($page == $lantais->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $lantais->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($lantais->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $lantais->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>
                @endif

                {{-- Last Page Link --}}
                @if ($lantais->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $lantais->url($lantais->lastPage()) }}">»</a>
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
        <h3>Belum Ada Lantai</h3>
        <p>Klik tombol "Tambah Lantai" untuk memulai</p>
    </div>
    @endif
</div>

@if(Auth::guard('stafaset')->user()->isAdmin())
<!-- Modal Tambah Lantai -->
<div id="addLantaiModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Lantai Baru</h3>
            <span class="close" onclick="closeAddLantaiModal()">&times;</span>
        </div>
        <form action="{{ route('lantai.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_lantai">Nama Lantai <span style="color: red;">*</span></label>
                <input type="text" id="nama_lantai" name="nama_lantai" placeholder="Contoh: Lantai 1, Lantai 2, Basement" required>
            </div>
            <div class="form-group">
                <label for="urutan">Urutan</label>
                <input type="number" id="urutan" name="urutan" placeholder="Urutan lantai (opsional)">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Lantai -->
<div id="editLantaiModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Lantai</h3>
            <span class="close" onclick="closeEditLantaiModal()">&times;</span>
        </div>
        <form id="editLantaiForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit_nama_lantai">Nama Lantai <span style="color: red;">*</span></label>
                <input type="text" id="edit_nama_lantai" name="nama_lantai" required>
            </div>
            <div class="form-group">
                <label for="edit_urutan">Urutan</label>
                <input type="number" id="edit_urutan" name="urutan">
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
    function openAddLantaiModal() { 
        document.getElementById('addLantaiModal').style.display = 'block'; 
    }
    
    function closeAddLantaiModal() { 
        document.getElementById('addLantaiModal').style.display = 'none'; 
    }
    
    function openEditLantaiModal(id, nama, urutan, keterangan) {
        document.getElementById('editLantaiForm').action = '/lantai/' + id;
        document.getElementById('edit_nama_lantai').value = nama;
        document.getElementById('edit_urutan').value = urutan;
        document.getElementById('edit_keterangan').value = keterangan || '';
        document.getElementById('editLantaiModal').style.display = 'block';
    }
    
    function closeEditLantaiModal() {
        document.getElementById('editLantaiModal').style.display = 'none';
    }
    
    window.onclick = function(event) { 
        const addModal = document.getElementById('addLantaiModal'); 
        const editModal = document.getElementById('editLantaiModal');
        if (event.target == addModal) { 
            addModal.style.display = 'none'; 
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
    }
</script>
@endsection