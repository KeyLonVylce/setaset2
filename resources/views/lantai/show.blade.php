@extends('layouts.app')

@section('title', $lantai->nama_lantai . ' - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-header h2 { font-size: 28px; color: #333; }
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
    .delete-btn { background: none; border: none; color: #dc3545; cursor: pointer; font-size: 20px; padding: 0; }
    .delete-btn:hover { color: #c82333; }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / {{ $lantai->nama_lantai }}
</div>

<div class="card">
    <div class="page-header">
        <h2>{{ $lantai->nama_lantai }}</h2>
        @if(Auth::guard('stafaset')->user()->isAdmin())
            <button class="btn btn-primary" onclick="openAddRuanganModal()">+ Tambah Ruangan</button>
        @endif
    </div>

    @if($lantai->keterangan || $lantai->ruangans->count() > 0)
    <div class="lantai-info">
        @if($lantai->keterangan)
        <p><strong>Keterangan:</strong> {{ $lantai->keterangan }}</p>
        @endif
        <p><strong>Total Ruangan:</strong> {{ $lantai->ruangans->count() }}</p>
        <p><strong>Total Barang:</strong> {{ $lantai->ruangans->sum('barangs_count') }}</p>
    </div>
    @endif

    @if($lantai->ruangans->count() > 0)
    <div class="ruangan-grid">
        @foreach($lantai->ruangans as $ruangan)
        <div class="ruangan-card">
            <div class="ruangan-card-header">
                <div>
                    <h3>{{ $ruangan->nama_ruangan }}</h3>
                    <span class="badge badge-info">{{ $ruangan->barangs_count }} Barang</span>
                </div>
                @if(Auth::guard('stafaset')->user()->isAdmin())
                <form action="{{ route('ruangan.delete', $ruangan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus ruangan ini? Semua barang di dalamnya akan ikut terhapus!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn" title="Hapus Ruangan">×</button>
                </form>
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
    @else
    <div class="empty-state">
        <h3>Belum Ada Ruangan</h3>
        <p>Klik tombol "Tambah Ruangan" untuk memulai</p>
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
                <input type="text" id="nama_ruangan" name="nama_ruangan" placeholder="Contoh: Ruang Server, Ruang Meeting" required>
            </div>
            <div class="form-group">
                <label for="penanggung_jawab">Penanggung Jawab</label>
                <input type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Nama penanggung jawab">
            </div>
            <div class="form-group">
                <label for="nip_penanggung_jawab">NIP Penanggung Jawab</label>
                <input type="text" id="nip_penanggung_jawab" name="nip_penanggung_jawab" placeholder="NIP">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
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
    
    window.onclick = function(event) { 
        const modal = document.getElementById('addRuanganModal'); 
        if (event.target == modal) { 
            modal.style.display = 'none'; 
        } 
    }
</script>
@endsection