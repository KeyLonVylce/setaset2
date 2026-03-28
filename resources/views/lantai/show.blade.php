@extends('layouts.app')

@section('title', $lantai->nama_lantai . ' - SETASET')

@section('styles')
<style>
    /* (CSS yang sudah ada, termasuk breadcrumb, header, dll, dipertahankan) */
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #0066cc; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
    .page-header h2 { font-size: 28px; color: #0066cc; margin: 0; }
    .search-box { display: flex; align-items: center; gap: 10px; }
    .search-box form { margin: 0; }
    .search-box input { padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; width: 250px; font-size: 14px; transition: border-color 0.3s; }
    .search-box input:focus { outline: none; border-color: #0066cc; }
    .lantai-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .lantai-info p { margin: 5px 0; color: #666; }

    /* ===== Gaya Kartu Ruangan (sama seperti kartu Lantai di home) ===== */
    .ruangan-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 20px; 
        margin-top: 20px; 
    }
    
    .ruangan-card-wrapper { 
        position: relative; 
    }
    
    .ruangan-card { 
        background: white; 
        padding: 30px; 
        border-radius: 16px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 2px solid transparent;
        text-align: center; 
        cursor: pointer; 
        transition: all 0.3s; 
        text-decoration: none; 
        color: #333; 
        display: block;
        position: relative;
        overflow: hidden;
    }
    
    .ruangan-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0066cc 0%, #004c99 100%);
        transform: scaleX(0);
        transition: transform 0.3s;
    }
    
    .ruangan-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 30px rgba(0, 102, 204, 0.2);
        border-color: #0066cc;
    }
    
    .ruangan-card:hover::before {
        transform: scaleX(1);
    }
    
    .ruangan-card h3 { 
        font-size: 22px; 
        margin-bottom: 15px; 
        color: #0066cc;
        font-weight: 700;
    }
    
    .ruangan-card .badge { 
        display: inline-block; 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: 13px; 
        font-weight: 600; 
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0066cc;
        margin: 5px;
        border: 1px solid rgba(0, 102, 204, 0.2);
    }
    
    .ruangan-card p { 
        color: #6b7280; 
        font-size: 14px;
        margin-top: 10px;
        line-height: 1.5;
    }
    
    /* Aksi edit/hapus (absolute, sama seperti di home) */
    .ruangan-card-actions { 
        position: absolute; 
        top: 15px; 
        right: 15px; 
        display: flex; 
        gap: 8px; 
        z-index: 10; 
    }
    
    .ruangan-card-actions button { 
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer; 
        font-size: 16px; 
        padding: 8px; 
        color: #6b7280;
        transition: all 0.3s;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .ruangan-card-actions button:hover { 
        background: white;
        color: #0066cc;
        border-color: #0066cc;
        transform: scale(1.1);
    }
    
    .ruangan-card-actions .btn-delete:hover { 
        color: #dc3545; 
        border-color: #dc3545; 
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 16px;
        margin: 20px 0;
    }

    .empty-state::before {
        content: '❌';
        font-size: 64px;
        display: block;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 22px;
        color: #1f2937;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .empty-state p {
        font-size: 15px;
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    /* ============================================
       MODAL (sama seperti di home, sudah ada)
    ============================================ */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal.active { display: flex; }
    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        overflow: hidden;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-30px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-header {
        background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    .modal-header .close {
        font-size: 28px;
        font-weight: 300;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
        line-height: 1;
    }
    .modal-header .close:hover { opacity: 1; }
    .modal-body { padding: 24px; }
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
    }
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding: 0 24px 24px;
        background: white;
    }
    .modal-footer .btn {
        padding: 10px 20px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #0066cc, #004c99);
        color: white;
        border: none;
        box-shadow: 0 2px 6px rgba(0, 102, 204, 0.3);
    }
    .modal-footer .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
    }
    .modal-footer .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    .modal-footer .btn-secondary:hover { background: #e5e7eb; }

    /* Pagination (sama seperti sebelumnya) */
    .pagination-wrapper { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding: 20px 0; flex-wrap: wrap; gap: 15px; }
    .pagination-info { color: #666; font-size: 14px; }
    .pagination-nav { display: flex; }
    .pagination { display: flex; list-style: none; gap: 5px; padding: 0; margin: 0; align-items: center; }
    .page-item { display: inline-block; }
    .page-link { display: flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border: 1px solid #ddd; border-radius: 50%; color: #666; text-decoration: none; transition: all 0.2s; background: white; font-size: 14px; cursor: pointer; }
    .page-link:hover { background: #f5f5f5; border-color: #bbb; }
    .page-item.active .page-link { background: #0066cc; color: white; border-color: #0066cc; font-weight: 600; cursor: default; }
    .page-item.disabled .page-link { color: #ccc; cursor: not-allowed; background: #fafafa; border-color: #e5e5e5; }

    @media (max-width: 768px) {
        .pagination-wrapper { justify-content: center; }
        .pagination-info { width: 100%; text-align: center; }
        .modal-content { width: 95%; margin: 0 auto; }
        .modal-header { padding: 16px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 0 20px 20px; }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / {{ $lantai->nama_lantai }}
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