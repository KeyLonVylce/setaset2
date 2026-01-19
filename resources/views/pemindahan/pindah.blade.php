@extends('layouts.app')

@section('title', 'Pemindahan Barang - SETASET')

@section('styles')
<style>
    .pemindahan-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .pemindahan-header {
        text-align: center;
        padding: 30px 20px;
        background: orange;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .pemindahan-header h1 {
        font-size: 28px;
        margin: 0 0 10px 0;
        font-weight: 600;
    }
    
    .pemindahan-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .pemindahan-card {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: none;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
    
    .alert ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background: white;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
        font-family: inherit;
    }
    
    .current-location-box {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 15px 20px;
        margin-top: 8px;
    }
    
    .current-location-box .label-small {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
        display: block;
    }
    
    .current-location-box .location-text {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .location-icon {
        display: inline-block;
        width: 20px;
        height: 20px;
        background: #667eea;
        border-radius: 50%;
        text-align: center;
        line-height: 20px;
        color: white;
        font-size: 12px;
        margin-right: 8px;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 35px;
        padding-top: 25px;
        border-top: 2px solid #f0f0f0;
    }
    
    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: orange;
        color: white;
        flex: 1;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-secondary {
        background: #e9ecef;
        color: #495057;
    }
    
    .btn-secondary:hover {
        background: #dee2e6;
    }
    
    @media (max-width: 768px) {
        .pemindahan-card {
            padding: 25px 20px;
        }
        
        .pemindahan-header h1 {
            font-size: 24px;
        }
        
        .form-actions {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="pemindahan-container">
    <div class="pemindahan-header">
        <p>Pindahkan barang dari satu lokasi ke lokasi lainnya</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pemindahan-card">
        <form id="moveForm" action="{{ route('pemindahan.pindah.store') }}" method="POST">
            @csrf

            {{-- PILIH BARANG --}}
            <div class="form-group">
                <label for="barang_id">
                    Pilih Barang yang Akan Dipindahkan
                </label>
                <select name="barang_id" id="barang_id" class="form-control" required>
                    <option value="">-- Pilih barang --</option>
                    @foreach($barangs as $barang)
                        <option 
                            value="{{ $barang->id }}"
                            data-current-location="{{ $barang->ruangan->nama_ruangan ?? 'Tidak ada' }}"
                            data-from-id="{{ $barang->ruangan->id ?? '' }}"
                        >
                            {{ $barang->nama_barang }} ({{ $barang->kode_barang ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- LOKASI SEKARANG --}}
            <div class="form-group">
                <label>
                    Lokasi Sekarang
                </label>
                <div class="current-location-box">
                    <span class="label-small">Barang saat ini berada di:</span>
                    <p id="currentLocation" class="location-text">Pilih barang terlebih dahulu</p>
                </div>
                <input type="hidden" name="from_location_id" id="from_location_id" value="">
            </div>

            {{-- PILIH LOKASI TUJUAN --}}
            <div class="form-group">
                <label for="ruangan_tujuan">
                    Lokasi Tujuan
                </label>
                <select name="ruangan_tujuan" id="ruangan_tujuan" class="form-control" required>
                    <option value="">-- Pilih lokasi tujuan --</option>
                    @foreach($ruangans as $ruang)
                        <option value="{{ $ruang->id }}">
                            {{ $ruang->nama_ruangan }} — Lantai {{ $ruang->lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- CATATAN --}}
            <div class="form-group">
                <label for="notes">
                    Catatan (Opsional)
                </label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    class="form-control" 
                    rows="3"
                    placeholder="Tambahkan catatan pemindahan jika diperlukan..."
                >{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    ← Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    Konfirmasi Pemindahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const barangSelect = document.getElementById('barang_id');
    const currentLocation = document.getElementById('currentLocation');
    const fromInput = document.getElementById('from_location_id');
    const form = document.getElementById('moveForm');

    function updateCurrent(option) {
        if (!option || !option.value) {
            currentLocation.innerHTML = '<span style="color: #999;">Pilih barang terlebih dahulu</span>';
            fromInput.value = '';
            return;
        }

        const locName = option.dataset.currentLocation || '-';
        const fromId = option.dataset.fromId || '';

        currentLocation.innerHTML = `<span class="location-icon"></span>${locName}`;
        fromInput.value = fromId;
    }

    barangSelect.addEventListener('change', function () {
        updateCurrent(this.selectedOptions[0]);
    });

    form.addEventListener('submit', function (e) {
        const barangText = barangSelect.selectedOptions[0]?.text || '';
        const fromText = currentLocation.textContent || '-';
        const toSelect = document.getElementById('ruangan_tujuan');
        const toText = toSelect.selectedOptions[0]?.text || '';

        if (!confirm(`Konfirmasi Pemindahan Barang\n\nBarang: ${barangText}\nDari: ${fromText}\nKe: ${toText}\n\nApakah Anda yakin ingin melanjutkan?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection