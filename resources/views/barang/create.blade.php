@extends('layouts.app')

@section('title', 'Tambah Barang - SETASET')

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
    
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $ruangan->lantai) }}">{{ $ruangan->lantai }}</a> / 
    <a href="{{ route('ruangan.show', $ruangan->id) }}">{{ $ruangan->nama_ruangan }}</a> / 
    Tambah Barang
</div>

<div class="card">
    <div class="page-header">
        <h2>Tambah Barang Baru</h2>
        <p style="color: #666;">Ruangan: <strong>{{ $ruangan->nama_ruangan }}</strong> ({{ $ruangan->lantai }})</p>
    </div>

    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">

        <div class="form-grid">
            <div class="form-group">
                <label for="no_urut">Nomor Urut</label>
                <input type="number" id="no_urut" name="no_urut" placeholder="Opsional">
                <div class="helper-text">Nomor urut untuk pengurutan</div>
            </div>

            <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" name="kode_barang" placeholder="Contoh: BRG-001">
                <div class="helper-text">Kode unik identifikasi barang</div>
            </div>

            <div class="form-group-full">
                <label for="nama_barang">Nama Barang <span style="color: red;">*</span></label>
                <input type="text" id="nama_barang" name="nama_barang" placeholder="Contoh: Laptop, Meja Kantor" required>
            </div>

            <div class="form-group">
                <label for="merk_model">Merk/Model</label>
                <input type="text" id="merk_model" name="merk_model" placeholder="Contoh: Dell Latitude 5420">
            </div>

            <div class="form-group">
                <label for="no_seri_pabrik">Nomor Seri Pabrik</label>
                <input type="text" id="no_seri_pabrik" name="no_seri_pabrik" placeholder="Serial number">
            </div>

            <div class="form-group">
                <label for="ukuran">Ukuran</label>
                <input type="text" id="ukuran" name="ukuran" placeholder="Contoh: 14 inch, 120x80 cm">
            </div>

            <div class="form-group">
                <label for="bahan">Bahan</label>
                <input type="text" id="bahan" name="bahan" placeholder="Contoh: Kayu Jati, Plastik">
            </div>

            <div class="form-group">
                <label for="tahun_pembuatan">Tahun Pembuatan</label>
                <input type="number" id="tahun_pembuatan" name="tahun_pembuatan" min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
            </div>

            <div class="form-group">
                <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                <input type="text" id="harga_perolehan" name="harga_perolehan" placeholder="0 atau -">
                <div class="helper-text">Harga perolehan per unit (bisa menggunakan strip "-" jika tidak ada harga)</div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span style="color: red;">*</span></label>
                <input type="number" id="jumlah" name="jumlah" min="1" value="1" required>
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
            <button type="submit" class="btn btn-success">Simpan Barang</button>
            <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Handle kondisi radio buttons
    const jumlahInput = document.getElementById('jumlah');
    const kondisiRadios = document.querySelectorAll('input[name="kondisi"]');
    const keadaanBaik = document.getElementById('keadaan_baik');
    const keadaanKurangBaik = document.getElementById('keadaan_kurang_baik');
    const keadaanRusakBerat = document.getElementById('keadaan_rusak_berat');

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

    // Auto-refresh CSRF token every 5 minutes to prevent 419 error
    setInterval(function() {
        fetch('/refresh-csrf')
            .then(response => response.json())
            .then(data => {
                document.querySelector('input[name="_token"]').value = data.token;
            })
            .catch(error => console.log('CSRF refresh failed:', error));
    }, 300000); // 5 minutes
</script>
@endsection