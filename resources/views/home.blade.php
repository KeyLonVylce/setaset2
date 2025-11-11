@extends('layouts.app')

@section('title', 'Home - SETASET')

@section('styles')
<style>
    .welcome-card { text-align: center; padding: 40px; background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); color: white; border-radius: 10px; margin-bottom: 30px; }
    .welcome-card h2 { font-size: 28px; margin-bottom: 10px; }
    .lantai-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .lantai-header h3 { font-size: 24px; color: #333; }
    .lantai-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
    .lantai-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; color: #333; display: block; }
    .lantai-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(255,123,61,0.3); }
    .lantai-card h4 { font-size: 20px; margin-bottom: 10px; color: #ff7b3d; }
    .lantai-card p { color: #666; font-size: 14px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
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
        <button class="btn btn-primary" onclick="openAddLantaiModal()">+ Tambah Lantai</button>
    </div>

    @if($lantaiList->count() > 0)
    <div class="lantai-grid">
        @foreach($lantaiList as $lantai)
        <a href="{{ route('lantai.show', $lantai) }}" class="lantai-card">
            <h4>{{ $lantai }}</h4>
            <p>Lihat ruangan</p>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <h3>Belum Ada Lantai</h3>
        <p>Klik tombol "Tambah Lantai" untuk memulai</p>
    </div>
    @endif
</div>

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
                <label for="nama_lantai">Nama Lantai</label>
                <input type="text" id="nama_lantai" name="nama_lantai" placeholder="Contoh: Lantai 1, Lantai 2, Basement" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openAddLantaiModal() { document.getElementById('addLantaiModal').style.display = 'block'; }
    function closeAddLantaiModal() { document.getElementById('addLantaiModal').style.display = 'none'; }
    window.onclick = function(event) { const modal = document.getElementById('addLantaiModal'); if (event.target == modal) { modal.style.display = 'none'; } }
</script>
@endsection