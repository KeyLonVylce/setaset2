@extends('layouts.app')

@section('title', 'Pemindahan Barang - SETASET')

@section('styles')
<style>
    .pemindahan-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .pemindahan-header {
        text-align: center;
        padding: 30px 20px;
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: white;
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
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        position: relative;
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
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .form-control.error {
        border-color: #dc3545;
        background-color: #fff8f8;
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

    .info-box {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 15px 20px;
        margin-top: 8px;
    }

    .info-box .label-small {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
        display: block;
    }

    .info-box .info-text {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
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
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: white;
        flex: 1;
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
    }

    /* Tombol Kembali - transisi warna latar saja, tidak berubah jadi putih */
    .btn-secondary {
        background: #e9ecef;
        color: #495057;
        border: 1px solid #dee2e6;
    }

    .btn-secondary:hover {
        background: #ced4da; /* warna abu-abu lebih gelap, bukan putih */
        border-color: #adb5bd;
        transform: translateX(-3px);
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    @media (max-width: 768px) {
        .pemindahan-card {
            padding: 25px 20px;
        }

        .pemindahan-header h1 {
            font-size: 24px;
        }

        .form-actions,
        .form-row {
            flex-direction: column;
            grid-template-columns: 1fr;
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
        <form id="moveForm" action="{{ route('barang.pindah.store') }}" method="POST">
            @csrf

            <div class="form-group" id="group_lantai_asal">
                <label for="lantai_asal">Pilih Lantai Asal <span class="text-danger">*</span></label>
                <select id="lantai_asal" class="form-control">
                    <option value="">-- Pilih lantai --</option>
                    @foreach($lantais as $lantai)
                        <option value="{{ $lantai->id }}">{{ $lantai->nama_lantai }}</option>
                    @endforeach
                </select>
                <div class="error-message" id="error_lantai_asal"></div>
            </div>

            <div class="form-group" id="group_ruangan_asal">
                <label for="ruangan_asal">Pilih Ruangan Asal <span class="text-danger">*</span></label>
                <select id="ruangan_asal" class="form-control" disabled>
                    <option value="">-- Pilih lantai terlebih dahulu --</option>
                </select>
                <div class="error-message" id="error_ruangan_asal"></div>
            </div>

            <div class="form-group" id="group_barang">
                <label for="barang_id">Pilih Barang yang Akan Dipindahkan <span class="text-danger">*</span></label>
                <select name="barang_id" id="barang_id" class="form-control" required disabled>
                    <option value="">-- Pilih ruangan terlebih dahulu --</option>
                </select>
                <div class="error-message" id="error_barang"></div>
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
                <div class="form-group" id="group_jumlah">
                    <label for="jumlah_pindah">Jumlah yang Dipindahkan <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_pindah" id="jumlah_pindah" class="form-control" min="1" placeholder="Masukkan jumlah" required disabled>
                    <div class="error-message" id="error_jumlah"></div>
                </div>

                <div class="form-group" id="group_ruangan_tujuan">
                    <label for="ruangan_tujuan">Ruangan Tujuan <span class="text-danger">*</span></label>
                    <select name="ruangan_tujuan" id="ruangan_tujuan" class="form-control" required>
                        <option value="">-- Pilih lokasi tujuan --</option>
                    </select>
                    <div class="error-message" id="error_ruangan_tujuan"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Tambahkan catatan pemindahan jika diperlukan...">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('pemindahan.laporanpindahbarang') }}" class="btn btn-dark">
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
    // DOM elements
    const lantaiAsal    = document.getElementById('lantai_asal');
    const ruanganAsal   = document.getElementById('ruangan_asal');
    const barangSelect  = document.getElementById('barang_id');
    const jumlahInput   = document.getElementById('jumlah_pindah');
    const stockInfo     = document.getElementById('stock_info');
    const stockAmount   = document.getElementById('stock_amount');
    const submitBtn     = document.getElementById('submitBtn');
    const ruanganTujuan = document.getElementById('ruangan_tujuan');
    const form          = document.getElementById('moveForm');

    // Data from server
    const ruangans = @json($ruangans);
    const barangs  = @json($barangs);

    // Helper: clear error untuk satu field
    function clearErrorField(fieldId) {
        const group = document.getElementById(`group_${fieldId}`);
        if (group) {
            const input = group.querySelector('.form-control');
            if (input) input.classList.remove('error');
            const errorDiv = group.querySelector('.error-message');
            if (errorDiv) errorDiv.textContent = '';
        }
    }

    // Helper: show error untuk satu field
    function showError(fieldId, message) {
        const group = document.getElementById(`group_${fieldId}`);
        if (group) {
            const input = group.querySelector('.form-control');
            if (input) input.classList.add('error');
            const errorDiv = group.querySelector('.error-message');
            if (errorDiv) errorDiv.textContent = message;
        }
    }

    // Reset form ketika dependency berubah
    function resetFormFields() {
        stockInfo.style.display = 'none';
        jumlahInput.disabled = true;
        jumlahInput.value = '';
        clearErrorField('jumlah');
        clearErrorField('barang');
        clearErrorField('ruangan_tujuan');
    }

    // Update dropdown ruangan tujuan (exclude asal)
    function updateRuanganTujuan() {
        const asalId = ruanganAsal.value;
        ruanganTujuan.innerHTML = '<option value="">-- Pilih lokasi tujuan --</option>';
        ruangans.forEach(r => {
            if (r.id != asalId) {
                ruanganTujuan.innerHTML += `<option value="${r.id}">${r.nama_ruangan} (${r.lantai_nama})</option>`;
            }
        });
        // setelah update, cek apakah tujuan yang dipilih sebelumnya masih valid? tidak perlu, biar user pilih ulang
    }

    // Event listeners untuk menghilangkan error saat field berubah/terisi
    lantaiAsal.addEventListener('change', function () {
        clearErrorField('lantai_asal');
        // reset turunan
        ruanganAsal.innerHTML = '<option value="">-- Pilih ruangan --</option>';
        barangSelect.innerHTML = '<option value="">-- Pilih ruangan terlebih dahulu --</option>';
        barangSelect.disabled = true;
        resetFormFields();
        if (this.value) {
            const filtered = ruangans.filter(r => r.lantai_id == this.value);
            filtered.forEach(r => {
                ruanganAsal.innerHTML += `<option value="${r.id}">${r.nama_ruangan}</option>`;
            });
            ruanganAsal.disabled = false;
        } else {
            ruanganAsal.disabled = true;
        }
    });

    ruanganAsal.addEventListener('change', function () {
        clearErrorField('ruangan_asal');
        resetFormFields();
        if (this.value) {
            const filtered = barangs.filter(b => b.ruangan_id == this.value);
            if (filtered.length > 0) {
                barangSelect.innerHTML = '<option value="">-- Pilih barang --</option>';
                filtered.forEach(b => {
                    barangSelect.innerHTML += `<option value="${b.id}" data-jumlah="${b.jumlah}">${b.nama_barang} (${b.kode_barang || '-'})</option>`;
                });
                barangSelect.disabled = false;
            } else {
                barangSelect.innerHTML = '<option value="">-- Tidak ada barang --</option>';
                barangSelect.disabled = true;
            }
        } else {
            barangSelect.disabled = true;
        }
        // reset error ruangan tujuan karena asal berubah
        clearErrorField('ruangan_tujuan');
    });

    barangSelect.addEventListener('change', function () {
        clearErrorField('barang');
        if (this.value) {
            const stok = parseInt(this.selectedOptions[0].dataset.jumlah);
            stockAmount.textContent = stok;
            stockInfo.style.display = 'block';
            jumlahInput.disabled = false;
            jumlahInput.max = stok;
            jumlahInput.value = '';
            updateRuanganTujuan();
        } else {
            resetFormFields();
        }
    });

    // Real-time validasi jumlah
    jumlahInput.addEventListener('input', function () {
        if (this.value) {
            const val = parseInt(this.value);
            const max = parseInt(this.max);
            if (isNaN(val) || val <= 0) {
                showError('jumlah', 'Jumlah harus lebih dari 0');
            } else if (val > max) {
                showError('jumlah', `Maksimal ${max} unit.`);
            } else {
                clearErrorField('jumlah');
            }
        } else {
            clearErrorField('jumlah');
        }
    });

    // Real-time validasi ruangan tujuan (tidak boleh sama dengan asal)
    ruanganTujuan.addEventListener('change', function () {
        const asalId = ruanganAsal.value;
        if (this.value && this.value === asalId) {
            showError('ruangan_tujuan', 'Ruangan tujuan tidak boleh sama dengan ruangan asal.');
        } else if (this.value) {
            clearErrorField('ruangan_tujuan');
        } else {
            clearErrorField('ruangan_tujuan');
        }
    });

    // Fungsi validasi penuh sebelum submit
    function validateForm() {
        let isValid = true;

        // Lantai asal
        if (!lantaiAsal.value) {
            showError('lantai_asal', 'Lantai asal harus dipilih.');
            isValid = false;
        } else {
            clearErrorField('lantai_asal');
        }

        // Ruangan asal
        if (!ruanganAsal.value) {
            showError('ruangan_asal', 'Ruangan asal harus dipilih.');
            isValid = false;
        } else {
            clearErrorField('ruangan_asal');
        }

        // Barang
        if (!barangSelect.value) {
            showError('barang', 'Barang harus dipilih.');
            isValid = false;
        } else {
            clearErrorField('barang');
        }

        // Jumlah
        const jumlah = parseInt(jumlahInput.value);
        const maxStok = jumlahInput.max ? parseInt(jumlahInput.max) : 0;
        if (!jumlahInput.value || isNaN(jumlah) || jumlah <= 0) {
            showError('jumlah', 'Jumlah harus diisi dengan angka positif.');
            isValid = false;
        } else if (jumlah > maxStok) {
            showError('jumlah', `Jumlah tidak boleh melebihi stok (${maxStok} unit).`);
            isValid = false;
        } else {
            clearErrorField('jumlah');
        }

        // Ruangan tujuan
        const tujuan = ruanganTujuan.value;
        if (!tujuan) {
            showError('ruangan_tujuan', 'Ruangan tujuan harus dipilih.');
            isValid = false;
        } else if (tujuan === ruanganAsal.value) {
            showError('ruangan_tujuan', 'Ruangan tujuan tidak boleh sama dengan ruangan asal.');
            isValid = false;
        } else {
            clearErrorField('ruangan_tujuan');
        }

        return isValid;
    }

    // Submit handler
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) {
            // scroll ke error pertama
            const firstError = document.querySelector('.form-control.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Konfirmasi
        const barangText = barangSelect.selectedOptions[0]?.text || '';
        const jumlah = jumlahInput.value;
        const ruanganAsalText = ruanganAsal.selectedOptions[0]?.text || '';
        const ruanganTujuanText = ruanganTujuan.selectedOptions[0]?.text || '';

        showConfirm({
            title: 'Konfirmasi Pemindahan Barang',
            message: `<div style="line-height:1.9;font-size:14px;text-align:left;">
                <div><strong>Barang:</strong> ${barangText}</div>
                <div><strong>Jumlah:</strong> ${jumlah} unit</div>
                <div><strong>Dari:</strong> ${ruanganAsalText}</div>
                <div><strong>Ke:</strong> ${ruanganTujuanText}</div>
            </div>`,
            type: 'warning',
            confirmText: '🚀 Ya, Pindahkan',
            onConfirm: () => form.submit()
        });
        });
});
</script>
@endsection