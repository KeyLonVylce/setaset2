@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pemindahan Barang</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <form id="moveForm" action="{{ route('pemindahan.pindah.store') }}" method="POST">
        @csrf

        {{-- PILIH BARANG --}}
        <div class="form-group">
            <label for="barang_id">Pilih Barang</label>
            <select name="barang_id" id="barang_id" class="form-control" required>
                <option value="">-- pilih barang --</option>
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
            <label>Lokasi Sekarang</label>
            <p id="currentLocation" class="font-weight-bold">-</p>
            <input type="hidden" name="from_location_id" id="from_location_id" value="">
        </div>

        {{-- PILIH LOKASI TUJUAN --}}
        <div class="form-group">
            <label for="ruangan_tujuan">Pilih Lokasi Tujuan</label>
            <select name="ruangan_tujuan" id="ruangan_tujuan" class="form-control" required>
                <option value="">-- pilih lokasi tujuan --</option>
                @foreach($ruangans as $ruang)
                    <option value="{{ $ruang->id }}">
                        {{ $ruang->nama_ruangan }} — Lantai {{ $ruang->lantai }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- CATATAN --}}
        <div class="form-group">
            <label for="notes">Catatan (opsional)</label>
            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Konfirmasi Pemindahan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const barangSelect = document.getElementById('barang_id');
    const currentLocation = document.getElementById('currentLocation');
    const fromInput = document.getElementById('from_location_id');
    const form = document.getElementById('moveForm');

    function updateCurrent(option) {
        if (!option || !option.value) {
            currentLocation.textContent = '-';
            fromInput.value = '';
            return;
        }

        const locName = option.dataset.currentLocation || '-';
        const fromId = option.dataset.fromId || '';

        currentLocation.textContent = locName;
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

        if (!confirm(`Pindahkan "${barangText}"\ndari: ${fromText}\nke: ${toText}\n\nLanjutkan?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
