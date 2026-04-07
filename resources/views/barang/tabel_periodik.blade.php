@extends('layouts.app')

@section('title', 'Tabel Periodik Barang')

@section('styles')
<style>
    .header-box {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .filter-box {
        background: white;
        padding: 20px 24px;
        border-radius: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .filter-box:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .table-box {
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow-x: auto;
    }
    .table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 12px;
        color: #1e293b;
        vertical-align: middle;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 12px;
        font-size: 0.9rem;
    }
    .badge {
        padding: 6px 12px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-danger {
        background: #fee2e2;
        color: #7f1d1d;
    }
    .badge-warning {
        background: #fedfaa;
        color: #92400e;
    }
    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }
    .sort-btn {
        background: none;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        padding: 0 4px;
        margin-left: 4px;
        color: #94a3b8;
        transition: color 0.2s;
        vertical-align: middle;
    }
    .sort-btn:hover {
        color: #28a745;
    }
    .sort-active {
        color: #28a745;
        font-weight: bold;
    }
    select, input[type="number"], input[type="date"] {
        border-radius: 40px;
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        background-color: white;
        transition: 0.2s;
    }
    select:focus, input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(40,167,69,0.2);
    }
    /* Breadcrumb style tanpa underline */
    .custom-breadcrumb {
        background: transparent;
        padding: 0 0 16px 0;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .custom-breadcrumb a {
        text-decoration: none;
        color: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
    }
    .custom-breadcrumb a:hover {
        text-decoration: underline;
    }
    .custom-breadcrumb .separator {
        margin: 0 6px;
        color: #6c757d;
    }
    .custom-breadcrumb .current {
        color: #6c757d;
    }
    .btn-excel {
        background: #28a745;
        color: white;
        border-radius: 40px;
        padding: 8px 20px;
        border: none;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-excel:hover {
        background: #218838;
        transform: translateY(-1px);
        color: white;
    }
    @media (max-width: 768px) {
        .filter-box form {
            flex-direction: column;
        }
        select, input, .btn-excel {
            width: 100%;
        }
    }
</style>
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

            <a href="{{ route('laporan.periodik.export', request()->all()) }}" class="btn-excel" style="margin-left:auto;">
                📎 Export Excel
            </a>
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
                @forelse($logs as $log)
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