@extends('layouts.app')

@section('title', 'Edit Barang - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { margin-bottom: 30px; }
    .page-header h2 { font-size: 28px; color: #333; }

    .form-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 20px; 
    }

    .form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 14px;
    }

    textarea { resize: vertical; }

    .helper-text {
        font-size: 12px;
        margin-top: 5px;
        color: #666;
    }

    .form-actions { margin-top: 20px; display: flex; gap: 10px; }

    /* Currency input */
    .currency-input-wrapper { position: relative; }
    .currency-prefix { 
        position: absolute; 
        left: 12px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: #666; 
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
        <p style="color:#666;">
            Ruangan: <strong>{{ $barang->ruangan->nama_ruangan }}</strong> ({{ $barang->ruangan->lantai }})
        </p>
    </div>

    <form action="{{ route('barang.update', $barang->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Hidden untuk harga dan kondisi --}}
        <input type="hidden" id="harga_perolehan_raw" name="harga_perolehan" value="{{ old('harga_perolehan', $barang->harga_perolehan) }}">
        <input type="hidden" id="keadaan_baik" name="keadaan_baik" value="0">
        <input type="hidden" id="keadaan_kurang_baik" name="keadaan_kurang_baik" value="0">
        <input type="hidden" id="keadaan_rusak_berat" name="keadaan_rusak_berat" value="0">

        <div class="form-grid">

            <div class="form-group">
                <label>Nomor Urut</label>
                <input type="number" name="no_urut" value="{{ old('no_urut', $barang->no_urut) }}">
            </div>

            <div class="form-group">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}">
            </div>

            <div class="form-group">
                <label>Nama Barang <span style="color:red">*</span></label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                <div class="helper-text">Masukkan nama barang yang jelas</div>
            </div>

            <div class="form-group">
                <label>Merk/Model</label>
                <input type="text" name="merk_model" value="{{ old('merk_model', $barang->merk_model) }}">
            </div>

            <div class="form-group">
                <label>Nomor Seri Pabrik</label>
                <input type="text" name="no_seri_pabrik" value="{{ old('no_seri_pabrik', $barang->no_seri_pabrik) }}">
            </div>

            <div class="form-group">
                <label>Ukuran</label>
                <input type="text" name="ukuran" value="{{ old('ukuran', $barang->ukuran) }}">
            </div>

            <div class="form-group">
                <label>Bahan</label>
                <input type="text" name="bahan" value="{{ old('bahan', $barang->bahan) }}">
            </div>

            <div class="form-group">
                <label>Tahun Pembuatan</label>
                <input type="number" name="tahun_pembuatan" min="1900" max="{{ date('Y') }}"
                       value="{{ old('tahun_pembuatan', $barang->tahun_pembuatan) }}">
            </div>

            {{-- Harga Perolehan --}}
            <div class="form-group">
                <label for="harga_perolehan_display">Harga Perolehan (Rp)</label>
                <div class="currency-input-wrapper">
                    <span class="currency-prefix">Rp</span>
                    <input type="text" id="harga_perolehan_display" class="currency-input" placeholder="0">
                </div>
                <div class="helper-text">Harga per unit</div>
            </div>

            <div class="form-group">
                <label>Jumlah <span style="color:red">*</span></label>
                <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $barang->jumlah) }}" required>
            </div>

            {{-- Dropdown Kondisi (menyamai create) --}}
            <div class="form-group">
                <label for="kondisi">Kondisi Barang <span style="color:red">*</span></label>
                <select name="kondisi" id="kondisi" required>
                    <option value="B" {{ $barang->kondisi=='B'?'selected':'' }}>Baik (B)</option>
                    <option value="KB" {{ $barang->kondisi=='KB'?'selected':'' }}>Kurang Baik (KB)</option>
                    <option value="RB" {{ $barang->kondisi=='RB'?'selected':'' }}>Rusak Berat (RB)</option>
                </select>
            </div>

            {{-- Keterangan --}}
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="4">{{ old('keterangan', $barang->keterangan) }}</textarea>
                <div class="helper-text">Opsional</div>
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
    // Format harga
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
