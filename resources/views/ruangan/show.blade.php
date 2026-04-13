@extends('layouts.app') 
<!-- Menggunakan template utama (layout) -->

@section('title', $ruangan->nama_ruangan . ' - SETASET') 
<!-- Set title halaman -->

@section('styles')
<link rel="stylesheet" href="{{ asset('css/ruangan/show.css') }}">
<!-- Load CSS dari folder public -->
@endsection

@section('content')

{{-- Breadcrumb (navigasi lokasi halaman) --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">
        {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
        <!-- optional() supaya tidak error kalau null -->
    </a> /
    
    <span>{{ $ruangan->nama_ruangan ?? '-' }}</span>
</div>

<div class="card">

    {{-- HEADER HALAMAN --}}
    <div class="page-header">
        <h2>{{ $ruangan->nama_ruangan }}</h2>

        <div class="action-flex">

            {{-- Cek apakah user admin --}}
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <a href="{{ route('ruangan.export', $ruangan->id) }}" 
                   class="btn btn-success" target="_blank">
                   📄 Export
                </a>
            @endif

            <a href="{{ route('pindah.form') }}" class="btn btn-success">
                ✏️ Pindahkan Barang
            </a>

            <a href="{{ route('barang.create', $ruangan->id) }}" class="btn btn-primary">
                + Tambah Barang
            </a>

            <a href="{{ route('barang.import.form', $ruangan->id) }}" class="btn btn-primary">
                ⬆️ Import Excel
            </a>
        </div>
    </div>

    {{-- SECTION INFO --}}
    <div class="info-grid">

        <!-- Info kiri -->
        <div class="info-section">
            <p><strong>Lantai:</strong> 
                {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
            </p>

            {{-- Kalau ada penanggung jawab --}}
            @if($ruangan->penanggungJawab)
                <p><strong>Penanggung Jawab:</strong> {{ $ruangan->penanggungJawab->nama }}</p>
                <p><strong>NIP:</strong> {{ $ruangan->penanggungJawab->nip ?? '-' }}</p>
                <p><strong>Jabatan:</strong> {{ $ruangan->penanggungJawab->jabatan ?? '-' }}</p>
            @endif

            {{-- Kalau ada keterangan --}}
            @if($ruangan->keterangan)
                <p><strong>Keterangan:</strong> {{ $ruangan->keterangan }}</p>
            @endif

            <p><strong>Total Barang:</strong> {{ $barangs->total() }} item</p>
            <!-- total() = jumlah data pagination -->
        </div>

        <!-- Chart kanan -->
        <div class="chart-section">
            <h3>📊 Kondisi Barang</h3>
            <canvas id="kondisiChart"></canvas>
            <!-- Chart.js akan render di sini -->
        </div>
    </div>

    {{-- SEARCH --}}
    <div class="search-box">
        <form method="GET">
            <!-- GET supaya query masuk ke URL -->
            <input type="text" name="search" 
                   placeholder="Cari barang..." 
                   value="{{ request('search') }}">
        </form>

        <!-- Tombol mode select -->
        <button type="button" id="toggleSelectBtn" class="btn btn-primary">✓ Pilih</button>
        <button type="button" id="cancelSelectBtn" class="btn btn-secondary" style="display:none;">
            ✕ Batal
        </button>
    </div>

    {{-- Kalau ada data --}}
    @if($barangs->count() > 0)

    {{-- FORM DELETE MASSAL --}}
    <form id="bulkDeleteForm" action="{{ route('barang.bulk.destroy') }}" method="POST">
        @csrf <!-- keamanan -->
        @method('DELETE') <!-- spoof method -->
        <input type="hidden" name="ids" id="selectedIdsInput">
    </form>

    {{-- AKSI BULK --}}
    <div id="bulkActions" class="bulk-actions">
        <span id="selectedCount">0</span> barang dipilih
        <button type="button" class="btn btn-danger" id="deleteSelectedBtn">
            🗑️ Hapus Terpilih
        </button>
    </div>

    {{-- TABEL --}}
    <table class="table">
        <thead>
            <tr>
                <!-- Checkbox pilih semua -->
                <th><input type="checkbox" id="selectAllCheckbox"></th>

                <th>No</th>
                <th>Kode</th>

                <th>
                    Nama Barang
                    <!-- Sorting -->
                    <a href="{{ request()->fullUrlWithQuery([
                        'direction' => ($direction == 'asc' ? 'desc' : 'asc')
                    ]) }}">
                        @if($direction == 'asc') ▲ @else ▼ @endif
                    </a>
                </th>

                <th>Merk</th>
                <th>No Seri</th>
                <th>Ukuran</th>
                <th>Bahan</th>
                <th>Tahun</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Harga</th>
                <th>Total</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($barangs as $i => $b)
            <tr>
                <!-- Checkbox per item -->
                <td>
                    <input type="checkbox" value="{{ $b->id }}" class="barang-checkbox">
                </td>

                <!-- Nomor pagination -->
                <td>{{ $barangs->firstItem() + $i }}</td>

                <td>{{ $b->kode_barang ?? '-' }}</td>
                <td>{{ $b->nama_barang }}</td>

                <td>{{ $b->merk_model ?? '-' }}</td>
                <td>{{ $b->no_seri_pabrik ?? '-' }}</td>
                <td>{{ $b->ukuran ?? '-' }}</td>
                <td>{{ $b->bahan ?? '-' }}</td>
                <td>{{ $b->tahun_pembuatan ?? '-' }}</td>

                <td>{{ $b->jumlah }}</td>

                <!-- Kondisi -->
                <td>
                    @if($b->kondisi === 'B')
                        Baik
                    @elseif($b->kondisi === 'KB')
                        Kurang Baik
                    @elseif($b->kondisi === 'RB')
                        Rusak Berat
                    @endif
                </td>

                <!-- Format harga -->
                <td>
                    @if($b->harga_perolehan)
                        Rp {{ number_format((float)$b->harga_perolehan, 0, ',', '.') }}
                    @endif
                </td>

                <!-- Total nilai -->
                <td>
                    @if($b->total_nilai)
                        Rp {{ number_format((float)$b->total_nilai, 0, ',', '.') }}
                    @endif
                </td>

                <td>{{ $b->keterangan ?? '-' }}</td>

                <!-- Aksi -->
                <td>
                    <a href="{{ route('barang.edit', $b->id) }}">Edit</a>

                    <form action="{{ route('barang.destroy', $b->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- PAGINATION --}}
    {{ $barangs->links() }}
    <!-- Laravel pagination -->

    @else
        <p>Tidak ada barang</p>
    @endif

</div>
@endsection

@section('scripts')

<!-- Load Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script>
    // Ambil data dari Laravel ke JS
    const barangs = @json($barangs->items());

    let baik = 0, kb = 0, rb = 0;

    // Loop data barang
    barangs.forEach(b => {
        if (b.kondisi === 'B') baik += parseInt(b.jumlah);
        else if (b.kondisi === 'KB') kb += parseInt(b.jumlah);
        else if (b.kondisi === 'RB') rb += parseInt(b.jumlah);
    });

    const total = baik + kb + rb;

    const ctx = document.getElementById('kondisiChart');

    if (total > 0) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Baik','Kurang Baik','Rusak Berat'],
                datasets: [{
                    data: [baik, kb, rb]
                }]
            }
        });
    }

    // SELECT MODE
    let selectMode = false;

    document.getElementById('toggleSelectBtn').onclick = () => {
        selectMode = true;
    };

</script>

@endsection