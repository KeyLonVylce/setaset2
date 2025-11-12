@extends('layouts.app')

@section('title', 'Edit Barang - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { margin-bottom: 30px; }
    .page-header h2 { font-size: 28px; color: #333; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-group-full { grid-column: 1 / -1; }
    .form-actions { display: flex; gap: 10px; margin-top: 20px; }
    .helper-text { font-size: 12px; color: #666; margin-top: 5px; }
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $barang->ruangan->lantai) }}">{{ $barang->ruangan->lantai }}</a> / 
    <a href="{{ route('ruangan.show', $barang->ruangan->id) }}">{{ $barang->ruangan->nama_ruangan }}</a> / 
    Edit Barang
</div>

<div class="card">
    <div class="page-header">
        <h2>Edit Barang</h2>
        <p style="color: #666;">Ruangan: <strong>{{ $barang->ruangan->nama_ruangan }}</strong> ({{ $barang->ruangan->lantai }})</p>
    </div>

    <form action="{{ route('barang.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="no_urut">Nomor Urut</label>
                <input type="number" id="no_urut" name="no_urut" value="{{ old('no_urut', $barang->no_urut) }}">
                <div class="helper-text">Nomor urut untuk pengurutan</div>
            </div>

            <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" placeholder="Contoh: BRG-001">
                <div class="helper-text">Kode unik identifikasi barang</div>
            </div>

            <div class="form-group-full">
                <label for="nama_barang">Nama Barang <span style="color: red;">*</span></label>
                <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
            </div>

            <div class="form-group">
                <label for="merk_model">Merk/Model</label>
                <input type="text" id="merk_model" name="merk_model" value="{{ old('merk_model', $barang->merk_model) }}">
            </div>

            <div class="form-group">
                <label for="no_seri_pabrik">Nomor Seri Pabrik</label>
                <input type="text" id="no_seri_pabrik" name="no_seri_pabrik" value="{{ old('no_seri_pabrik', $barang->no_seri_pabrik) }}">
            </div>

            <div class="form-group">
                <label for="ukuran">Ukuran</label>
                <input type="text" id="ukuran" name="ukuran" value="{{ old('ukuran', $barang->ukuran) }}">
            </div>

            <div class="form-group">
                <label for="bahan">Bahan</label>
                <input type="text" id="bahan" name="bahan" value="{{ old('bahan', $barang->bahan) }}">
            </div>

            <div class="form-group">
                <label for="tahun_pembuatan">Tahun Pembuatan</label>
                <input type="number" id="tahun_pembuatan" name="tahun_pembuatan" value="{{ old('tahun_pembuatan', $barang->tahun_pembuatan) }}" min="1900" max="{{ date('Y') }}">
            </div>

            <div class="form-group">
                <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                <input type="number" id="harga_perolehan" name="harga_perolehan" value="{{ old('harga_perolehan', $barang->harga_perolehan) }}" min="0" step="0.01">
                <div class="helper-text">Harga perolehan per unit</div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span style="color: red;">*</span></label>
                <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $barang->jumlah) }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="keadaan_baik">Kondisi Baik <span style="color: red;">*</span></label>
                <input type="number" id="keadaan_baik" name="keadaan_baik" value="{{ old('keadaan_baik', $barang->keadaan_baik) }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="keadaan_kurang_baik">Kondisi Kurang Baik <span style="color: red;">*</span></label>
                <input type="number" id="keadaan_kurang_baik" name="keadaan_kurang_baik" value="{{ old('keadaan_kurang_baik', $barang->keadaan_kurang_baik) }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="keadaan_rusak_berat">Kondisi Rusak Berat <span style="color: red;">*</span></label>
                <input type="number" id="keadaan_rusak_berat" name="keadaan_rusak_berat" value="{{ old('keadaan_rusak_berat', $barang->keadaan_rusak_berat) }}" min="0" required>
            </div>

            <div class="form-group-full">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="4">{{ old('keterangan', $barang->keterangan) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Update Barang</button>
            <a href="{{ route('ruangan.show', $barang->ruangan->id) }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto-validate kondisi barang = jumlah
    const jumlah = document.getElementById('jumlah');
    const baik = document.getElementById('keadaan_baik');
    const kurangBaik = document.getElementById('keadaan_kurang_baik');
    const rusakBerat = document.getElementById('keadaan_rusak_berat');

    function validateTotal() {
        const total = parseInt(jumlah.value) || 0;
        const totalKondisi = (parseInt(baik.value) || 0) + 
                            (parseInt(kurangBaik.value) || 0) + 
                            (parseInt(rusakBerat.value) || 0);
        
        if (totalKondisi !== total) {
            alert('Total kondisi barang (Baik + Kurang Baik + Rusak Berat) harus sama dengan jumlah total!');
            return false;
        }
        return true;
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!validateTotal()) {
            e.preventDefault();
        }
    });
</script>
@endsection