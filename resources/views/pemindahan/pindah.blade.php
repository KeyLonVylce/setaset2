@extends('layouts.app')

@section('title', 'Pemindahan Barang - SETASET')

@section('styles')
<style>
    .pemindahan-container {
        max-width: 900px; margin: 0 auto; }
    
    .pemindahan-header {
        text-align: center; padding: 30px 20px; background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); color: white; border-radius: 10px; margin-bottom: 30px;
    }
    
    .pemindahan-header h1 {
        font-size: 28px; margin: 0 0 10px 0; font-weight: 600;
    }
    
    .pemindahan-header p {
        margin: 0; opacity: 0.9; font-size: 14px;
    }
    
    .pemindahan-card {
        background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .alert {
        padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border: none;
    }
    
    .alert-success {
        background: #d4edda; color: #155724; border-left: 4px solid #28a745;
    }
    
    .alert-danger {
        background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545;
    }
    
    .alert ul {
        margin: 0; padding-left: 20px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block; font-weight: 600; color: #333; margin-bottom: 8px; font-size: 14px;
    }
    
    .form-control {
        width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s; background: white;
    }
    
    .form-control:focus {
        outline: none; border-color: #ff7b3d; box-shadow: 0 0 0 3px rgba(255, 123, 61, 0.1);
    }
    
    select.form-control {
        cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; padding-right: 40px;
    }
    
    textarea.form-control {
        resize: vertical; min-height: 80px; font-family: inherit; 
    }
    
    .info-box {
        background: #f8f9fa; border: 2px solid #e9ecef; border-radius: 8px; padding: 15px 20px; margin-top: 8px;
    }
    
    .info-box .label-small {
        font-size: 12px; color: #666; margin-bottom: 5px; display: block;
    }
    
    .info-box .info-text {
        font-size: 16px; font-weight: 600; color: #333; margin: 0;
    }
    
    .stock-info {
        display: inline-block; background: #d1ecf1; color: #0c5460; padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-top: 5px; 
    }
    .form-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    
    .form-actions {
        display: flex; gap: 10px; margin-top: 35px; padding-top: 25px; border-top: 2px solid #f0f0f0;
    }
    
    .btn {
        padding: 12px 30px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-block;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #ff9a56 0%, #ff7b3d 100%); color: white; flex: 1;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 123, 61, 0.4);
    }
    
    .btn-secondary {
        background: #e9ecef; color: #495057;
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
        
        .form-actions, .form-row {
            flex-direction: column; grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="pemindahan-container">
    <div class="pemindahan-header">
        <h1>Pemindahan Barang</h1>
        <p>Pindahkan barang dari satu lokasi ke lokasi lainnya</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
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

            <div class="form-group">
                <label for="lantai_asal">
                    Pilih Lantai Asal
                </label>
                <select id="lantai_asal" class="form-control">
                    <option value="">-- Pilih lantai --</option>
                    @foreach($lantais as $lantai)
                        <option value="{{ $lantai->id }}">{{ $lantai->nama_lantai }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="ruangan_asal">
                    Pilih Ruangan Asal
                </label>
                <select id="ruangan_asal" class="form-control" disabled>
                    <option value="">-- Pilih lantai terlebih dahulu --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="barang_id">
                    Pilih Barang yang Akan Dipindahkan
                </label>
                <select name="barang_id" id="barang_id" class="form-control" required disabled>
                    <option value="">-- Pilih ruangan terlebih dahulu --</option>
                </select>
            </div>

            <div class="form-group" id="stock_info" style="display: none;">
                <div class="info-box">
                    <span class="label-small">Stok tersedia:</span>
                    <p class="info-text">
                        <span id="stock_amount">0</span> unit
                    </p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="jumlah_pindah">
                        Jumlah yang Dipindahkan
                    </label>
                    <input 
                        type="number" 
                        name="jumlah_pindah" 
                        id="jumlah_pindah" 
                        class="form-control" 
                        min="1"
                        placeholder="Masukkan jumlah"
                        required
                        disabled
                    >
                </div>

                <div class="form-group">
                    <label for="ruangan_tujuan">
                        Ruangan Tujuan
                    </label>
                    <select name="ruangan_tujuan" id="ruangan_tujuan" class="form-control" required>
                        <option value="">-- Pilih lokasi tujuan --</option>
                        @foreach($ruangans as $ruang)
                            <option value="{{ $ruang->id }}">
                                {{ $ruang->nama_ruangan }} ({{ $ruang->lantai }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

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
                <button type="submit" class="btn btn-primary" id="submitBtn">
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
    const lantaiAsal = document.getElementById('lantai_asal');
    const ruanganAsal = document.getElementById('ruangan_asal');
    const barangSelect = document.getElementById('barang_id');
    const jumlahInput = document.getElementById('jumlah_pindah');
    const stockInfo = document.getElementById('stock_info');
    const stockAmount = document.getElementById('stock_amount');
    const submitBtn = document.getElementById('submitBtn');
    const ruanganTujuan = document.getElementById('ruangan_tujuan');
    const form = document.getElementById('moveForm');

    const ruangans = @json($ruangans);
    const barangs = @json($barangs);

    let selectedBarang = null;

    lantaiAsal.addEventListener('change', function() {
        const lantaiId = this.value;
        ruanganAsal.innerHTML = '<option value="">-- Pilih ruangan --</option>';
        barangSelect.innerHTML = '<option value="">-- Pilih ruangan terlebih dahulu --</option>';
        
        if (lantaiId) {
            const filteredRuangans = ruangans.filter(r => r.lantai_id == lantaiId);
            filteredRuangans.forEach(r => {
                ruanganAsal.innerHTML += `<option value="${r.id}">${r.nama_ruangan}</option>`;
            });
            ruanganAsal.disabled = false;
        } else {
            ruanganAsal.disabled = true;
            barangSelect.disabled = true;
            resetForm();
        }
    });

    ruanganAsal.addEventListener('change', function() {
        const ruanganId = this.value;
        barangSelect.innerHTML = '<option value="">-- Pilih barang --</option>';
        
        if (ruanganId) {
            const filteredBarangs = barangs.filter(b => b.ruangan_id == ruanganId);
            if (filteredBarangs.length > 0) {
                filteredBarangs.forEach(b => {
                    barangSelect.innerHTML += `<option value="${b.id}" data-jumlah="${b.jumlah}">${b.nama_barang} (${b.kode_barang || '-'})</option>`;
                });
                barangSelect.disabled = false;
            } else {
                barangSelect.innerHTML = '<option value="">-- Tidak ada barang --</option>';
                barangSelect.disabled = true;
            }
        } else {
            barangSelect.disabled = true;
            resetForm();
        }
    });

        barangSelect.addEventListener('change', function() {
        const selectedOption = this.selectedOptions[0];
        
        if (this.value) {
            const stok = parseInt(selectedOption.dataset.jumlah);
            selectedBarang = barangs.find(b => b.id == this.value);
            
            stockAmount.textContent = stok;
            stockInfo.style.display = 'block';
            jumlahInput.disabled = false;
            jumlahInput.max = stok;
            jumlahInput.value = '';
            
            updateRuanganTujuan();
        } else {
            resetForm();
        }
    });

    jumlahInput.addEventListener('input', function() {
        validateForm();
    });

    ruanganTujuan.addEventListener('change', function() {
        validateForm();
    });

    function updateRuanganTujuan() {
        const ruanganAsalId = ruanganAsal.value;
        ruanganTujuan.innerHTML = '<option value="">-- Pilih lokasi tujuan --</option>';
        
        ruangans.forEach(r => {
            if (r.id != ruanganAsalId) {
                ruanganTujuan.innerHTML += `<option value="${r.id}">${r.nama_ruangan} (${r.lantai})</option>`;
            }
        });
    }

    function validateForm() {
        const jumlah = parseInt(jumlahInput.value);
        const maxStok = parseInt(jumlahInput.max);
        const tujuan = ruanganTujuan.value;

        if (jumlah > 0 && jumlah <= maxStok && tujuan) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    function resetForm() {
        stockInfo.style.display = 'none';
        jumlahInput.disabled = true;
        jumlahInput.value = '';
        submitBtn.disabled = true;
        selectedBarang = null;
    }

    form.addEventListener('submit', function(e) {
        const barangText = barangSelect.selectedOptions[0]?.text || '';
        const jumlah = jumlahInput.value;
        const ruanganAsalText = ruanganAsal.selectedOptions[0]?.text || '';
        const ruanganTujuanText = ruanganTujuan.selectedOptions[0]?.text || '';

        if (!confirm(`Konfirmasi Pemindahan Barang\n\nBarang: ${barangText}\nJumlah: ${jumlah} unit\nDari: ${ruanganAsalText}\nKe: ${ruanganTujuanText}\n\nApakah Anda yakin ingin melanjutkan?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection