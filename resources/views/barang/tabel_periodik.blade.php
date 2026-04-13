@extends('layouts.app')

@section('title', 'Tabel Periodik Barang')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/tabel-periodik.css') }}">
@endsection

@section('content')
<div class="container">

    <!-- BREADCRUMB -->
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

    <!-- FILTER -->
    <div class="filter-box">
        <div style="margin-bottom:10px; font-weight:600;">🔍 Filter Data</div>

        <form id="filterForm" method="GET"
              style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">

            <!-- LANTAI -->
            <div class="filter-item">
                <label>Lantai</label>
                <select name="lantai" id="lantaiSelect">
                    <option value="">Semua Lantai</option>
                    @foreach($lantais as $l)
                        <option value="{{ $l->id }}"
                            {{ request('lantai') == $l->id ? 'selected' : '' }}>
                            {{ $l->nama_lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- RUANGAN — $ruangans adalah array dari controller -->
            <div class="filter-item">
                <label>Ruangan</label>
                <select name="ruangan" id="ruanganSelect" disabled>
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $r)
                        <option value="{{ $r['id'] }}"
                            data-lantai="{{ $r['lantai_id'] }}"
                            {{ request('ruangan') == $r['id'] ? 'selected' : '' }}>
                            {{ $r['nama_ruangan'] }} ({{ $r['lantai_nama'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- BULAN -->
            <div class="filter-item">
                <label>Bulan</label>
                <select name="bulan">
                    <option value="">Semua</option>
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                               7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember']
                              as $key => $b)
                        <option value="{{ $key }}"
                            {{ request('bulan') == $key ? 'selected' : '' }}>
                            {{ $b }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- TAHUN -->
            <div class="filter-item">
                <label>Tahun</label>
                <input type="number" name="tahun"
                       value="{{ request('tahun') }}" placeholder="2026">
            </div>

            <!-- TANGGAL AWAL -->
            <div class="filter-item">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>

            <!-- TANGGAL AKHIR -->
            <div class="filter-item">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
            <div>
                <a href="{{ route('laporan.periodik.export', request()->query()) }}"
                   class="btn btn-success">📥 Export Excel</a>
            </div>
        </form>
    </div>

    <!-- TABEL -->
    <div class="table-box">
        <h3>Riwayat Aktivitas</h3>
        <table class="table table-bordered" id="periodicTable">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Kode</th>
                    <th>Aktivitas</th>
                    <th>Dari</th>
                    <th>Ke</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $i => $log)

                {{-- ROW UTAMA --}}
                <tr class="data-row">
                    <td style="font-weight:500;">{{ $log['barang_nama'] }}</td>

                    <td style="font-size:12px; color:#6b7280;">
                        {{ $log['kode_barang'] ?? '—' }}
                    </td>

                    <td>
                        @if($log['aktivitas'] === 'tambah')
                            <span class="badge-akt badge-tambah">➕ Tambah</span>
                        @elseif($log['aktivitas'] === 'hapus')
                            <span class="badge-akt badge-hapus">🗑️ Hapus</span>
                        @elseif($log['aktivitas'] === 'edit')
                            <span class="badge-akt badge-edit">✏️ Edit</span>
                        @else
                            <span class="badge-akt badge-pindah">🔄 Pindah</span>
                        @endif
                    </td>

                    <td>
                        @if(!empty($log['dari']) && $log['dari'] !== '-')
                            <span class="room-tag">{{ $log['dari'] }}</span>
                            @if(!empty($log['lantai_dari']))
                                <div class="lantai-sub">{{ $log['lantai_dari'] }}</div>
                            @endif
                        @else
                            <span class="dash-empty">—</span>
                        @endif
                    </td>

                    <td>
                        @if(!empty($log['ke']) && $log['ke'] !== '-')
                            <span class="room-tag">{{ $log['ke'] }}</span>
                            @if(!empty($log['lantai_ke']))
                                <div class="lantai-sub">{{ $log['lantai_ke'] }}</div>
                            @endif
                        @else
                            <span class="dash-empty">—</span>
                        @endif
                    </td>

                    <td style="white-space:nowrap;" data-timestamp="{{ strtotime($log['created_at']) }}">
                        <div style="font-size:13px;">
                            {{ \Carbon\Carbon::parse($log['created_at'])->translatedFormat('d F Y') }}
                        </div>
                        <div style="font-size:11px; color:#9ca3af;">
                            {{ \Carbon\Carbon::parse($log['created_at'])->format('H:i') }}
                        </div>
                    </td>

                    <td>
                        <button class="btn-detail" onclick="toggleKet({{ $i }}, this)">
                            <span class="icon">▼</span> Detail
                        </button>
                    </td>
                </tr>

                {{-- ROW KETERANGAN --}}
                <tr class="ket-row" id="ket-{{ $i }}">
                    <td class="ket-cell" colspan="7">
                        <div class="ket-bubble">
                            {!! $log['keterangan'] ?? '<span style="color:#9ca3af">Tidak ada keterangan.</span>' !!}
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:#6b7280;">
                        Tidak ada data aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($paginator->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }}
            dari {{ $paginator->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                @if($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                @php
                    $current = $paginator->currentPage();
                    $last    = $paginator->lastPage();
                    $start   = max(1, $current - 2);
                    $end     = min($last, $current + 2);
                    if ($start > 1) echo '<li class="page-item"><a class="page-link" href="'.$paginator->url(1).'">1</a></li>';
                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                @endphp

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                @php
                    if ($end < $last - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    if ($end < $last) echo '<li class="page-item"><a class="page-link" href="'.$paginator->url($last).'">'.$last.'</a></li>';
                @endphp

                @if($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
                    </li>
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
const lantaiSelect  = document.getElementById('lantaiSelect');
const ruanganSelect = document.getElementById('ruanganSelect');

function filterRuanganByLantai() {
    const lantaiId = lantaiSelect.value;

    // Enable/disable dropdown ruangan
    ruanganSelect.disabled = !lantaiId;
    if (!lantaiId) ruanganSelect.value = '';

    // Tampilkan/sembunyikan opsi ruangan sesuai lantai
    Array.from(ruanganSelect.options).forEach(opt => {
        if (!opt.value) return;
        const cocok = opt.getAttribute('data-lantai') == lantaiId;
        opt.style.display = cocok ? '' : 'none';
        // Reset pilihan kalau ruangan aktif tidak cocok dengan lantai baru
        if (ruanganSelect.value == opt.value && !cocok) {
            ruanganSelect.value = '';
        }
    });
}

// Jalankan saat load — restore state dari query string
window.addEventListener('load', function () {
    const lantaiDipilih = lantaiSelect.value;
    if (lantaiDipilih) {
        // Ada lantai dari query string: enable ruangan dan filter opsinya
        ruanganSelect.disabled = false;
        Array.from(ruanganSelect.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = opt.getAttribute('data-lantai') == lantaiDipilih ? '' : 'none';
        });
    } else {
        // Tidak ada lantai: disable ruangan
        ruanganSelect.disabled = true;
    }
});

// Saat lantai berubah: filter ruangan dulu, baru submit
lantaiSelect.addEventListener('change', function () {
    filterRuanganByLantai();
    document.getElementById('filterForm').submit();
});

// Submit otomatis saat ruangan/bulan/tahun berubah
ruanganSelect.addEventListener('change', () => document.getElementById('filterForm').submit());
document.querySelector('select[name="bulan"]').addEventListener('change',
    () => document.getElementById('filterForm').submit());
document.querySelector('input[name="tahun"]').addEventListener('change',
    () => document.getElementById('filterForm').submit());

// Toggle keterangan
function toggleKet(index, btn) {
    const row  = document.getElementById('ket-' + index);
    const icon = btn.querySelector('.icon');
    const open = row.classList.toggle('open');
    icon.textContent        = open ? '▲' : '▼';
    btn.style.background    = open ? '#eff6ff' : '';
    btn.style.borderColor   = open ? '#93c5fd' : '';
    btn.style.color         = open ? '#1e40af' : '';
}

document.querySelector('input[name="start_date"]').addEventListener('change', () => document.getElementById('filterForm').submit());
document.querySelector('input[name="end_date"]').addEventListener('change', () => document.getElementById('filterForm').submit());

</script>
@endsection