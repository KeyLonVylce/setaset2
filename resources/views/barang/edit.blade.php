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

    .currency-input-wrapper { position: relative; }
    .currency-prefix { 
        position: absolute; 
        left: 12px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: #666; 
        font-size: 14px;
        pointer-events: none;
    }
    .currency-input { padding-left: 35px !important; }

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

        <input type="hidden" id="harga_perolehan_raw" name="harga_perolehan"
            value="{{ old('harga_perolehan', $barang->harga_perolehan) }}">

        <div class="form-grid">
            <div class="form-group">
                <label for="nama_barang">Nama Barang <span style="color:red">*</span></label>
                <input type="text" id="nama_barang" name="nama_barang"
                    value="{{ old('nama_barang', $barang->nama_barang) }}" required>
            </div>

            <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" name="kode_barang"
                    value="{{ old('kode_barang', $barang->kode_barang) }}">
            </div>

            <div class="form-group">
                <label for="merk_model">Merk/Model</label>
                <input type="text" id="merk_model" name="merk_model"
                    value="{{ old('merk_model', $barang->merk_model) }}">
            </div>

            <div class="form-group">
                <label for="no_seri_pabrik">Nomor Seri Pabrik</label>
                <input type="text" id="no_seri_pabrik" name="no_seri_pabrik"
                    value="{{ old('no_seri_pabrik', $barang->no_seri_pabrik) }}">
            </div>

            <div class="form-group">
                <label for="ukuran">Ukuran</label>
                <input type="text" id="ukuran" name="ukuran"
                    value="{{ old('ukuran', $barang->ukuran) }}">
            </div>

            <div class="form-group">
                <label for="bahan">Bahan</label>
                <input type="text" id="bahan" name="bahan"
                    value="{{ old('bahan', $barang->bahan) }}">
            </div>

            <div class="form-group">
                <label for="tahun_pembuatan">Tahun Pembuatan</label>
                <input type="number" id="tahun_pembuatan" name="tahun_pembuatan"
                    value="{{ old('tahun_pembuatan', $barang->tahun_pembuatan) }}"
                    min="1900" max="{{ date('Y') }}">
            </div>

            <div class="form-group">
                <label for="harga_perolehan_display">Harga Perolehan (Rp)</label>
                <div class="currency-input-wrapper">
                    <span class="currency-prefix">Rp</span>
                    <input type="text" id="harga_perolehan_display"
                        class="currency-input"
                        value="{{ old('harga_perolehan', $barang->harga_perolehan) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span style="color:red">*</span></label>
                <input type="number" id="jumlah" name="jumlah"
                    value="{{ old('jumlah', $barang->jumlah) }}" min="1" required>
            </div>

            <div class="form-group">
                <label for="kondisi">Kondisi</label>
                <select id="kondisi" name="kondisi" required>
                    <option value="B"  {{ old('kondisi',$barang->kondisi) == 'B'  ? 'selected':'' }}>Baik (B)</option>
                    <option value="KB" {{ old('kondisi',$barang->kondisi) == 'KB' ? 'selected':'' }}>Kurang Baik (KB)</option>
                    <option value="RB" {{ old('kondisi',$barang->kondisi) == 'RB' ? 'selected':'' }}>Rusak Berat (RB)</option>
                </select>
            </div>

            <div class="form-group form-group-full">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="4"
                    placeholder="Keterangan tambahan tentang barang">{{ old('keterangan', $barang->keterangan) }}</textarea>
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
const hargaDisplay = document.getElementById('harga_perolehan_display');
const hargaRaw = document.getElementById('harga_perolehan_raw');

function formatCurrency(value) {
    const numbers = value.replace(/\D/g, '');
    if (!numbers) return '';
    return new Intl.NumberFormat('id-ID').format(numbers);
}

window.addEventListener('DOMContentLoaded', function() {
    if (hargaRaw.value) {
        hargaDisplay.value = formatCurrency(hargaRaw.value);
    }
});

hargaDisplay.addEventListener('input', function(e) {
    const formatted = formatCurrency(e.target.value);
    e.target.value = formatted;
    hargaRaw.value = formatted.replace(/\./g, '') || '0';
});
</script>
@endsection