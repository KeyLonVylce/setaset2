@extends('layouts.app')

@section('title', 'Tabel Periodik Barang')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/tabel-periodik.css') }}">
@endsection

@section('content')
<div class="container">

    <div class="custom-breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span class="separator">/</span>
        <span class="current">Tabel Periodik Barang</span>
    </div>

    <div class="header-box">
        <h2>Tabel Periodik Barang</h2>
        <small>Audit aktivitas barang dan ringkasan kondisi inventaris</small>
    </div>

    <div class="audit-summary-grid">
        <div class="summary-card summary-blue">
            <div class="summary-label">Total Log Audit</div>
            <div class="summary-value">{{ number_format($summary['total_log']) }}</div>
            <div class="summary-note">Jumlah log setelah filter diterapkan</div>
        </div>
        <div class="summary-card summary-green">
            <div class="summary-label">Kondisi Baik</div>
            <div class="summary-value">{{ number_format($summary['kondisi']['B']) }}</div>
            <div class="summary-note">Barang kondisi baik</div>
        </div>
        <div class="summary-card summary-amber">
            <div class="summary-label">Kurang Baik</div>
            <div class="summary-value">{{ number_format($summary['kondisi']['KB']) }}</div>
            <div class="summary-note">Barang kondisi kurang baik</div>
        </div>
        <div class="summary-card summary-red">
            <div class="summary-label">Rusak Berat</div>
            <div class="summary-value">{{ number_format($summary['kondisi']['RB']) }}</div>
            <div class="summary-note">Barang rusak berat</div>
        </div>
    </div>

    <div class="audit-summary-grid audit-activity-grid">
        <div class="summary-card summary-soft">
            <div class="summary-label">Aktivitas Tambah</div>
            <div class="summary-value">{{ number_format($summary['aktivitas']['tambah']) }}</div>
            <div class="summary-note">Barang ditambahkan</div>
        </div>
        <div class="summary-card summary-soft">
            <div class="summary-label">Aktivitas Edit</div>
            <div class="summary-value">{{ number_format($summary['aktivitas']['edit']) }}</div>
            <div class="summary-note">Barang diperbarui</div>
        </div>
        <div class="summary-card summary-soft">
            <div class="summary-label">Aktivitas Hapus</div>
            <div class="summary-value">{{ number_format($summary['aktivitas']['hapus']) }}</div>
            <div class="summary-note">Barang dihapus</div>
        </div>
        <div class="summary-card summary-soft">
            <div class="summary-label">Aktivitas Pindah</div>
            <div class="summary-value">{{ number_format($summary['aktivitas']['pindah']) }}</div>
            <div class="summary-note">Barang dipindahkan</div>
        </div>
    </div>

    <div class="filter-box">
        <div class="filter-heading">Filter Data Audit</div>

        <form id="filterForm" method="GET" class="periodic-filter-form">
            <div class="filter-item">
                <label>Lantai</label>
                <select name="lantai" id="lantaiSelect">
                    <option value="">Semua Lantai</option>
                    @foreach($lantais as $l)
                        <option value="{{ $l->id }}" {{ request('lantai') == $l->id ? 'selected' : '' }}>
                            {{ $l->nama_lantai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label>Ruangan</label>
                <select name="ruangan" id="ruanganSelect" disabled>
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $r)
                        <option
                            value="{{ $r['id'] }}"
                            data-lantai="{{ $r['lantai_id'] }}"
                            {{ request('ruangan') == $r['id'] ? 'selected' : '' }}
                        >
                            {{ $r['nama_ruangan'] }} ({{ $r['lantai_nama'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label>Aktivitas</label>
                <select name="aktivitas" id="aktivitasSelect">
                    <option value="">Semua Aktivitas</option>
                    <option value="tambah" {{ request('aktivitas') == 'tambah' ? 'selected' : '' }}>Tambah</option>
                    <option value="edit" {{ request('aktivitas') == 'edit' ? 'selected' : '' }}>Edit</option>
                    <option value="hapus" {{ request('aktivitas') == 'hapus' ? 'selected' : '' }}>Hapus</option>
                    <option value="pindah" {{ request('aktivitas') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Kondisi Barang</label>
                <select name="kondisi" id="kondisiSelect">
                    <option value="">Semua Kondisi</option>
                    <option value="B" {{ request('kondisi') == 'B' ? 'selected' : '' }}>Baik</option>
                    <option value="KB" {{ request('kondisi') == 'KB' ? 'selected' : '' }}>Kurang Baik</option>
                    <option value="RB" {{ request('kondisi') == 'RB' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>

            <div class="filter-item">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <div class="filter-actions-inline">
                <button type="submit" class="btn btn-primary">Terapkan</button>
                <a href="{{ route('laporan.periodik.export', request()->query()) }}" class="btn btn-success">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="table-box">
        <h3>Riwayat Aktivitas</h3>
        <table class="table table-bordered" id="periodicTable">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Kode</th>
                    <th>Kondisi</th>
                    <th>Aktivitas</th>
                    <th>Dari</th>
                    <th>Ke</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $i => $log)
                <tr class="data-row">
                    <td style="font-weight:500;">{{ $log['barang_nama'] }}</td>

                    <td style="font-size:12px; color:#6b7280;">
                        {{ $log['kode_barang'] ?? '—' }}
                    </td>

                    <td>
                        @php $kondisi = $log['kondisi'] ?? null; @endphp
                        @if($kondisi === 'B')
                            <span class="badge-kondisi badge-kondisi-b">Baik</span>
                        @elseif($kondisi === 'KB')
                            <span class="badge-kondisi badge-kondisi-kb">Kurang Baik</span>
                        @elseif($kondisi === 'RB')
                            <span class="badge-kondisi badge-kondisi-rb">Rusak Berat</span>
                        @else
                            <span class="dash-empty">—</span>
                        @endif
                    </td>

                    <td>
                        @if($log['aktivitas'] === 'tambah')
                            <span class="badge-akt badge-tambah">Tambah</span>
                        @elseif($log['aktivitas'] === 'hapus')
                            <span class="badge-akt badge-hapus">Hapus</span>
                        @elseif($log['aktivitas'] === 'edit')
                            <span class="badge-akt badge-edit">Edit</span>
                        @else
                            <span class="badge-akt badge-pindah">Pindah</span>
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

                <tr class="ket-row" id="ket-{{ $i }}">
                    <td class="ket-cell" colspan="8">
                        <div class="ket-bubble">
                            {!! $log['keterangan'] ?? '<span style="color:#9ca3af">Tidak ada keterangan.</span>' !!}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#6b7280;">
                        Tidak ada data aktivitas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                    $last = $paginator->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                    if ($start > 1) echo '<li class="page-item"><a class="page-link" href="'.$paginator->url(1).'">1</a></li>';
                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                @endphp

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
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
const filterForm = document.getElementById('filterForm');
const lantaiSelect = document.getElementById('lantaiSelect');
const ruanganSelect = document.getElementById('ruanganSelect');
const aktivitasSelect = document.getElementById('aktivitasSelect');
const kondisiSelect = document.getElementById('kondisiSelect');
const startDateInput = document.querySelector('input[name="start_date"]');
const endDateInput = document.querySelector('input[name="end_date"]');

function filterRuanganByLantai() {
    const lantaiId = lantaiSelect.value;
    ruanganSelect.disabled = !lantaiId;

    if (!lantaiId) {
        ruanganSelect.value = '';
    }

    Array.from(ruanganSelect.options).forEach(opt => {
        if (!opt.value) return;
        const cocok = opt.getAttribute('data-lantai') == lantaiId;
        opt.style.display = !lantaiId || cocok ? '' : 'none';
        if (ruanganSelect.value == opt.value && !cocok) {
            ruanganSelect.value = '';
        }
    });
}

window.addEventListener('load', function () {
    filterRuanganByLantai();
});

lantaiSelect.addEventListener('change', function () {
    filterRuanganByLantai();
    filterForm.submit();
});

ruanganSelect.addEventListener('change', () => filterForm.submit());
aktivitasSelect.addEventListener('change', () => filterForm.submit());
kondisiSelect.addEventListener('change', () => filterForm.submit());
startDateInput.addEventListener('change', () => filterForm.submit());
endDateInput.addEventListener('change', () => filterForm.submit());

function toggleKet(index, btn) {
    const row = document.getElementById('ket-' + index);
    const icon = btn.querySelector('.icon');
    const open = row.classList.toggle('open');
    icon.textContent = open ? '▲' : '▼';
    btn.style.background = open ? '#eff6ff' : '';
    btn.style.borderColor = open ? '#93c5fd' : '';
    btn.style.color = open ? '#1e40af' : '';
}
</script>
@endsection
