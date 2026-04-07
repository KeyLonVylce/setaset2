@extends('layouts.app')

@section('title', 'Tabel Periodik Barang')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/tabel-periodik.css') }}">
@endsection

@section('content')
<div class="container">
    <!-- BREADCRUMB (diatas header) -->
    <div class="custom-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="separator">/</span>
        <span class="current">Tabel Periodik Barang</span>
    </div>

    <!-- HEADER -->
    <div class="header-box">
        <h2>📊 Tabel Periodik Barang</h2>
        <small>Histori aktivitas barang – pindah, tambah, edit, hapus</small>
    </div>

    <!-- FILTER (REALTIME) -->
    <div class="filter-box">
        <form id="filterForm" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <select name="lantai" id="lantaiSelect" style="min-width:140px;">
                <option value="">Semua Lantai</option>
                @foreach($lantais as $l)
                    <option value="{{ $l->id }}" {{ request('lantai') == $l->id ? 'selected' : '' }}>
                        {{ $l->nama_lantai }}
                    </option>
                @endforeach
            </select>

            <select name="ruangan" id="ruanganSelect" style="min-width:160px;" disabled>
                <option value="">Pilih Ruangan</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}" data-lantai="{{ $r->lantai_id }}"
                        {{ request('ruangan') == $r->id ? 'selected' : '' }}>
                        {{ $r->nama_ruangan }}
                    </option>
                @endforeach
            </select>

            <select name="bulan" style="min-width:130px;">
                <option value="">Bulan</option>
                @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $key => $b)
                    <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                        {{ $b }}
                    </option>
                @endforeach
            </select>

            <input type="number" name="tahun" placeholder="Tahun" value="{{ request('tahun') }}" style="width:110px;">

            <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Dari tanggal" style="width:150px;">
            <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="Sampai tanggal" style="width:150px;">

            <div style="margin-left:auto;">
                <a href="{{ route('laporan.periodik.export', request()->query()) }}" class="btn btn-success marg">Export Excel</a>
            </div>
        </form>
    </div>

    <!-- TABEL dengan tambahan kolom Kode Barang -->
    <div class="table-box">
        <table class="table table-bordered" id="periodicTable">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>
                        Barang
                        <span class="sort-btn" data-sort="barang-asc">▲</span>
                        <span class="sort-btn" data-sort="barang-desc">▼</span>
                    </th>
                    <th>Aktivitas</th>
                    <th>Ruangan</th>
                    <th>
                        Tanggal
                        <span class="sort-btn" data-sort="tanggal-asc">▲</span>
                        <span class="sort-btn" data-sort="tanggal-desc">▼</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $log)
                <tr>
                    <td>{{ $log['kode_barang'] ?? '-' }}</td>
                    <td class="barang-nama-cell">{{ $log['barang_nama'] }}</td>
                    <td>
                        @php $akt = $log['aktivitas']; @endphp
                        @if($akt === 'tambah')
                            <span class="badge badge-success">➕ Tambah</span>
                        @elseif($akt === 'hapus')
                            <span class="badge badge-danger">🗑️ Hapus</span>
                        @elseif($akt === 'edit')
                            <span class="badge badge-warning">✏️ Edit</span>
                        @else
                            <span class="badge badge-primary">🔄 Pindah</span>
                        @endif
                    </td>
                    <td>{{ $log['ruangan_display'] }}</td>
                    <td data-timestamp="{{ strtotime($log['created_at']) }}">
                        {{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada data ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
        {{-- Pagination --}}
        @if($paginator->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} entri
            </div>
            <div class="pagination-nav">
                <ul class="pagination">
                    {{-- Tombol Previous --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">‹</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a></li>
                    @endif

                    {{-- Nomor halaman dengan ellipsis --}}
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                        if ($start > 1) echo '<li class="page-item"><a class="page-link" href="'.$paginator->url(1).'">1</a></li>';
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    @endphp

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $current)
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @php
                        if ($end < $last - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        if ($end < $last) echo '<li class="page-item"><a class="page-link" href="'.$paginator->url($last).'">'.$last.'</a></li>';
                    @endphp

                    {{-- Tombol Next --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">›</span></li>
                    @endif
                </ul>
            </div>
        </div>
        @endif
</div>
@endsection

@section('scripts')
<script>
    // ======================== FILTER REALTIME ========================
    const lantaiSelect = document.getElementById('lantaiSelect');
    const ruanganSelect = document.getElementById('ruanganSelect');
    const bulanSelect = document.querySelector('select[name="bulan"]');
    const tahunInput = document.querySelector('input[name="tahun"]');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput = document.querySelector('input[name="end_date"]');

    function applyFilters() {
        const params = new URLSearchParams();
        if (lantaiSelect.value) params.set('lantai', lantaiSelect.value);
        if (ruanganSelect.value) params.set('ruangan', ruanganSelect.value);
        if (bulanSelect.value) params.set('bulan', bulanSelect.value);
        if (tahunInput.value) params.set('tahun', tahunInput.value);
        if (startDateInput.value) params.set('start_date', startDateInput.value);
        if (endDateInput.value) params.set('end_date', endDateInput.value);
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    lantaiSelect.addEventListener('change', function() {
        ruanganSelect.value = '';
        applyFilters();
    });
    ruanganSelect.addEventListener('change', applyFilters);
    bulanSelect.addEventListener('change', applyFilters);
    tahunInput.addEventListener('change', applyFilters);
    startDateInput.addEventListener('change', applyFilters);
    endDateInput.addEventListener('change', applyFilters);

    // ======================== FILTER RUANGAN BERDASARKAN LANTAI ========================
    function filterRuanganByLantai() {
        const lantaiId = lantaiSelect.value;
        if (!lantaiId) {
            ruanganSelect.disabled = true;
            ruanganSelect.value = '';
        } else {
            ruanganSelect.disabled = false;
        }
        Array.from(ruanganSelect.options).forEach(opt => {
            if (!opt.value) return;
            const belongs = opt.getAttribute('data-lantai') == lantaiId;
            opt.style.display = belongs ? 'block' : 'none';
            if (ruanganSelect.value === opt.value && !belongs) {
                ruanganSelect.value = '';
            }
        });
    }

    lantaiSelect.addEventListener('change', filterRuanganByLantai);
    window.addEventListener('load', filterRuanganByLantai);

    // ======================== SORTING (BARANG & TANGGAL) ========================
    const tbody = document.querySelector('#periodicTable tbody');
    const sortBtns = document.querySelectorAll('.sort-btn');

    function sortTable(criteria) {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length === 0 || (rows.length === 1 && rows[0].innerText.includes('Tidak ada data'))) return;

        const isAsc = criteria.includes('asc');
        const type = criteria.includes('barang') ? 'barang' : 'tanggal';

        rows.sort((rowA, rowB) => {
            if (type === 'barang') {
                // kolom Barang sekarang di index 1 (karena index 0 adalah kode barang)
                const nameA = rowA.cells[1].innerText.trim();
                const nameB = rowB.cells[1].innerText.trim();
                return isAsc ? nameA.localeCompare(nameB, 'id') : nameB.localeCompare(nameA, 'id');
            } else {
                const tsA = parseInt(rowA.cells[4].getAttribute('data-timestamp'));
                const tsB = parseInt(rowB.cells[4].getAttribute('data-timestamp'));
                return isAsc ? tsA - tsB : tsB - tsA;
            }
        });

        rows.forEach(row => tbody.appendChild(row));

        sortBtns.forEach(btn => btn.classList.remove('sort-active'));
        const activeBtn = document.querySelector(`.sort-btn[data-sort="${criteria}"]`);
        if (activeBtn) activeBtn.classList.add('sort-active');
    }

    sortBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const criteria = btn.getAttribute('data-sort');
            sortTable(criteria);
        });
    });
</script>
@endsection