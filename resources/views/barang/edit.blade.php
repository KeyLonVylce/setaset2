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
    
    /* Radio button styling */
    .radio-group { display: flex; flex-direction: column; gap: 12px; padding-top: 8px; }
    .radio-option { display: flex; align-items: center; cursor: pointer; }
    .radio-option input[type="radio"] { 
        width: 18px; 
        height: 18px; 
        margin-right: 10px; 
        cursor: pointer;
        accent-color: #ff7b3d;
    }
    .radio-option label { cursor: pointer; font-size: 14px; }
    
    /* Currency input styling */
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
        <input type="hidden" id="harga_perolehan_raw" name="harga_perolehan" value="{{ old('harga_perolehan', $barang->harga_perolehan) }}">

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
                <label for="harga_perolehan_display">Harga Perolehan (Rp)</label>
                <div class="currency-input-wrapper">
                    <span class="currency-prefix">Rp</span>
                    <input type="text" id="harga_perolehan_display" class="currency-input" placeholder="0">
                </div>
                <div class="helper-text">Harga perolehan per unit</div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span style="color: red;">*</span></label>
                <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $barang->jumlah) }}" min="1" required>
            </div>

<div class="form-group">
    <label for="kondisi">Kondisi Barang <span style="color: red;">*</span></label>
    <select id="kondisi" name="kondisi" required>
        <option value="B" selected>Baik (B)</option>
        <option value="KB">Kurang Baik (KB)</option>
        <option value="RB">Rusak Berat (RB)</option>
    </select>
</div>

<div class="form-group-full">
    <label for="keterangan">Keterangan</label>
    <textarea id="keterangan" name="keterangan" rows="4" placeholder="Keterangan tambahan tentang barang"></textarea>
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
    // Format currency input
    const hargaDisplay = document.getElementById('harga_perolehan_display');
    const hargaRaw = document.getElementById('harga_perolehan_raw');

    function formatCurrency(value) {
        const numbers = value.replace(/\D/g, '');
        if (!numbers) return '';
        return new Intl.NumberFormat('id-ID').format(numbers);
    }

    // Initialize display value on page load
    window.addEventListener('DOMContentLoaded', function() {
        const initialValue = hargaRaw.value;
        if (initialValue && initialValue !== '0') {
            hargaDisplay.value = formatCurrency(initialValue);
        }
    });

    hargaDisplay.addEventListener('input', function(e) {
        const formatted = formatCurrency(e.target.value);
        e.target.value = formatted;
        hargaRaw.value = formatted.replace(/\./g, '') || '0';
    });

    // Handle kondisi radio buttons
    const jumlahInput = document.getElementById('jumlah');
    const kondisiRadios = document.querySelectorAll('input[name="kondisi"]');
    const keadaanBaik = document.getElementById('keadaan_baik');
    const keadaanKurangBaik = document.getElementById('keadaan_kurang_baik');
    const keadaanRusakBerat = document.getElementById('keadaan_rusak_berat');

    // Set initial radio button based on existing data
    window.addEventListener('DOMContentLoaded', function() {
        const baik = parseInt(keadaanBaik.value) || 0;
        const kurangBaik = parseInt(keadaanKurangBaik.value) || 0;
        const rusakBerat = parseInt(keadaanRusakBerat.value) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;

        // Determine which condition has the matching value with jumlah
        if (baik === jumlah && baik > 0) {
            document.getElementById('kondisi_baik').checked = true;
        } else if (kurangBaik === jumlah && kurangBaik > 0) {
            document.getElementById('kondisi_kurang_baik').checked = true;
        } else if (rusakBerat === jumlah && rusakBerat > 0) {
            document.getElementById('kondisi_rusak_berat').checked = true;
        } else {
            // Default to baik if no match or all zero
            document.getElementById('kondisi_baik').checked = true;
        }
    });

    function updateKondisiValues() {
        const jumlah = parseInt(jumlahInput.value) || 0;
        const selectedKondisi = document.querySelector('input[name="kondisi"]:checked').value;

        keadaanBaik.value = '0';
        keadaanKurangBaik.value = '0';
        keadaanRusakBerat.value = '0';

        if (selectedKondisi === 'baik') {
            keadaanBaik.value = jumlah.toString();
        } else if (selectedKondisi === 'kurang_baik') {
            keadaanKurangBaik.value = jumlah.toString();
        } else if (selectedKondisi === 'rusak_berat') {
            keadaanRusakBerat.value = jumlah.toString();
        }
    }

    jumlahInput.addEventListener('input', updateKondisiValues);
    kondisiRadios.forEach(radio => {
        radio.addEventListener('change', updateKondisiValues);
    });

    // Validate on submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const jumlah = parseInt(jumlahInput.value) || 0;
        if (jumlah < 1) {
            alert('Jumlah barang harus minimal 1!');
            e.preventDefault();
            return false;
        }
    });
</script>
@endsectio