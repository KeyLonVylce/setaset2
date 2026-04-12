@extends('layouts.app')

@section('title', 'Tambah Barang - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/create.css') }}">
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">
    {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
</a> &nbsp;/ &nbsp;
    <a href="{{ route('ruangan.show', $ruangan->id) }}">{{ $ruangan->nama_ruangan }}</a> &nbsp;/&nbsp;
    Tambah Barang
</div>

<div class="card">
    <div class="page-header">
        <h2>Tambah Barang Baru</h2>
        <p style="color: #666; margin-top: 5px;">
    Ruangan: <strong>{{ $ruangan->nama_ruangan }}</strong> — 
    {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; color: #721c24;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('barang.store', $ruangan->id) }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="section-title">Identitas Barang</div>

            <div class="form-group">
                <label for="nama_barang">Nama Barang <span class="required">*</span></label>
                <input type="text" id="nama_barang" name="nama_barang"
                    value="{{ old('nama_barang') }}"
                    placeholder="Contoh: Meja Kerja"
                    required>
            </div>

            <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input type="text" id="kode_barang" name="kode_barang"
                    value="{{ old('kode_barang') }}"
                    placeholder="Contoh: MJ-001">
            </div>

            <div class="form-group">
                <label for="merk_model">Merk / Model</label>
                <input type="text" id="merk_model" name="merk_model"
                    value="{{ old('merk_model') }}"
                    placeholder="Contoh: Futura Type A">
            </div>

            <div class="form-group">
                <label for="no_seri_pabrik">No. Seri Pabrik</label>
                <input type="text" id="no_seri_pabrik" name="no_seri_pabrik"
                    value="{{ old('no_seri_pabrik') }}"
                    placeholder="Nomor seri dari pabrik">
            </div>

            <div class="section-title">Spesifikasi</div>

            <div class="form-group">
                <label for="ukuran">Ukuran</label>
                <input type="text" id="ukuran" name="ukuran"
                    value="{{ old('ukuran') }}"
                    placeholder="Contoh: 120x60x75 cm">
            </div>

            <div class="form-group">
                <label for="bahan">Bahan</label>
                <input type="text" id="bahan" name="bahan"
                    value="{{ old('bahan') }}"
                    placeholder="Contoh: Kayu, Besi, Plastik">
            </div>

            <div class="form-group">
                <label for="tahun_pembuatan">Tahun Pembuatan</label>
                <input type="number" id="tahun_pembuatan" name="tahun_pembuatan"
                    value="{{ old('tahun_pembuatan') }}"
                    min="1900" max="{{ date('Y') }}"
                    placeholder="{{ date('Y') }}">
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah <span class="required">*</span></label>
                <input type="number" id="jumlah" name="jumlah"
                    value="{{ old('jumlah', 1) }}"
                    min="1" required>
            </div>

            <div class="section-title">Kondisi & Nilai</div>

            <div class="form-group">
                <label for="kondisi">Kondisi <span class="required">*</span></label>
                <select id="kondisi" name="kondisi" required>
                    <option value="">-- Pilih Kondisi --</option>
                    <option value="B"  {{ old('kondisi') === 'B'  ? 'selected' : '' }}>Baik</option>
                    <option value="KB" {{ old('kondisi') === 'KB' ? 'selected' : '' }}>Kurang Baik</option>
                    <option value="RB" {{ old('kondisi') === 'RB' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div class="form-group">
                <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                <input type="number" id="harga_perolehan" name="harga_perolehan"
                    value="{{ old('harga_perolehan') }}"
                    min="0" step="1000"
                    placeholder="0">
            </div>

            <div class="form-group">
                <label for="total_nilai">Total Nilai (Rp)</label>
                <input type="number" id="total_nilai" name="total_nilai"
                    value="{{ old('total_nilai') }}"
                    min="0" step="1000"
                    placeholder="0" readonly style="background-color: #f9fafb;">
            </div>

            <div class="form-group full-width">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">💾 Simpan Barang</button>
            <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn btn-danger">Batal</a>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInput = document.getElementById('jumlah');
        const hargaInput = document.getElementById('harga_perolehan');
        const totalInput = document.getElementById('total_nilai');

        function updateTotal() {
            const jumlah = parseInt(jumlahInput.value) || 0;
            const harga = parseFloat(hargaInput.value) || 0;
            totalInput.value = jumlah * harga;
        }

        // Update saat input berubah
        jumlahInput.addEventListener('input', updateTotal);
        hargaInput.addEventListener('input', updateTotal);

        // Panggil sekali untuk mengisi nilai awal jika ada old value
        updateTotal();
    });
</script>
@endsection