@extends('layouts.app') 
<!-- Pakai layout utama -->

@section('title', 'Home - SETASET') 
<!-- Judul halaman -->

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<!-- Load CSS -->

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Biar responsive -->
@endsection

@section('content')

<!-- Card sambutan -->
<div class="welcome-card">
    <h2>Selamat Datang di SETASET</h2>
    <p>Sistem Inventaris Aset Diskominfo</p>
</div>

{{-- DASHBOARD --}}
<div class="dashboard-grid">

    <!-- Chart Pie -->
    <div class="chart-container">
        <h3>📊 Distribusi Kondisi Barang</h3>

        <canvas id="globalKondisiChart"></canvas>
        <!-- Chart.js render di sini -->
    </div>

    <!-- Chart Bar -->
    <div class="chart-container">
        <h3>📈 Top 5 Barang Terbanyak</h3>

        <canvas id="topBarangsChart"></canvas>
    </div>
</div>

<!-- Cek login -->
@auth

    <!-- Cek role admin -->
    @if(auth()->user()->role === 'admin')

    <div>
        <h3>Laporan</h3>

        <!-- Link laporan -->
        <a href="{{ route('laporan.periodik') }}">Tabel Periodik</a>
        <a href="{{ route('pemindahan.laporanpindahbarang') }}">
            Laporan Pindah Barang
        </a>
    </div>

    @endif
@endauth

<!-- CARD LANTAI -->
<div class="card">

    <h3>Daftar Lantai</h3>

    <!-- Tombol tambah (admin only) -->
    @if(Auth::guard('stafaset')->user()->isAdmin())
        <button onclick="openAddLantaiModal()">+ Tambah Lantai</button>
    @endif

    <!-- Cek data -->
    @if($lantais->count() > 0)

    <div class="lantai-grid">

        @foreach($lantais as $lantai)

        <div class="lantai-card-wrapper">

            <!-- ACTION ADMIN -->
            @if(Auth::guard('stafaset')->user()->isAdmin())

            <button onclick="openEditLantaiModal(
                {{ $lantai->id }},
                '{{ addslashes($lantai->nama_lantai) }}',
                '{{ addslashes($lantai->keterangan ?? '') }}'
            )">Edit</button>

            <!-- DELETE -->
            <form method="POST" action="{{ route('lantai.destroy', $lantai->id) }}">
                @csrf
                @method('DELETE')

                @if($lantai->ruangans_count > 0)
                    <!-- Tidak bisa hapus -->
                    <button type="button">Tidak Bisa Hapus</button>
                @else
                    <!-- Bisa hapus -->
                    <button type="submit">Hapus</button>
                @endif
            </form>

            @endif

            <!-- LINK KE DETAIL -->
            <a href="{{ route('lantai.show', $lantai->id) }}">

                <h4>{{ $lantai->nama_lantai }}</h4>

                <span>{{ $lantai->ruangans_count }} Ruangan</span>

                <!-- Keterangan -->
                @if($lantai->keterangan)
                    <p>
                        {{ Str::limit($lantai->keterangan, 50) }}
                        <!-- limit text -->
                    </p>
                @endif

            </a>

        </div>

        @endforeach
    </div>

    <!-- PAGINATION -->
    {{ $lantais->links() }}

    @else

    <!-- Kalau kosong -->
    <p>Belum ada lantai</p>

    @endif
</div>

<!-- MODAL TAMBAH -->
<div id="addLantaiModal">
    <form method="POST" action="{{ route('lantai.store') }}">
        @csrf

        <input type="text" name="nama_lantai" required>
        <textarea name="keterangan"></textarea>

        <button>Simpan</button>
    </form>
</div>

<!-- MODAL EDIT -->
<div id="editLantaiModal">
    <form id="editLantaiForm" method="POST">
        @csrf
        @method('PUT')

        <input type="text" id="edit_nama_lantai" name="nama_lantai">
        <textarea id="edit_keterangan" name="keterangan"></textarea>

        <button>Update</button>
    </form>
</div>

@endsection

@section('scripts')

<!-- Load Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script>

// ===== DATA DARI LARAVEL =====
const kondisiBaik = {{ $kondisiBaik ?? 0 }};
const kondisiKB = {{ $kondisiKurangBaik ?? 0 }};
const kondisiRB = {{ $kondisiRusakBerat ?? 0 }};
const total = {{ $totalBarang ?? 0 }};

// ===== PIE CHART =====
const ctx = document.getElementById('globalKondisiChart');

if (total > 0 && ctx) {
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Baik','KB','RB'],
            datasets: [{
                data: [kondisiBaik, kondisiKB, kondisiRB]
            }]
        }
    });
}

// ===== BAR CHART =====
const topData = @json($topBarangs ?? []);
const topCtx = document.getElementById('topBarangsChart');

if (topData.length > 0 && topCtx) {

    const labels = topData.map(i => i.nama_barang);
    const values = topData.map(i => i.total);

    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values
            }]
        }
    });
}

// ===== MODAL =====
function openAddLantaiModal() {
    document.getElementById('addLantaiModal').style.display = 'block';
}

function closeAddLantaiModal() {
    document.getElementById('addLantaiModal').style.display = 'none';
}

function openEditLantaiModal(id, nama, ket) {
    document.getElementById('editLantaiForm').action = '/admin/lantai/' + id;
    document.getElementById('edit_nama_lantai').value = nama;
    document.getElementById('edit_keterangan').value = ket;

    document.getElementById('editLantaiModal').style.display = 'block';
}

</script>

@endsection