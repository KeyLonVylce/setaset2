@extends('layouts.app')

@section('title', 'Laporan Bulanan Barang')

@section('styles')
<style>
.header-box {
    background: linear-gradient(135deg,#0066cc,#004c99);
    color:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

.filter-box {
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}

.filter-box form {
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
}

select, input {
    padding:8px 12px;
    border-radius:8px;
    border:1px solid #ddd;
}

.table-box {
    background:white;
    padding:20px;
    border-radius:12px;
}

.badge {
    background:#e0f2fe;
    color:#0369a1;
    padding:4px 10px;
    border-radius:20px;
}
</style>
@endsection


@section('content')

<div class="container">

    <!-- HEADER -->
    <div class="header-box">
        <h2>📦 Laporan Bulanan Barang</h2>

        <small>
            Periode:
            {{ $bulanList[(int)request('bulan')] ?? '-' }}
            {{ request('tahun') ?? '' }}
        </small>
    </div>

    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> / Lapornan Bulanan Barang
    </div>

    <form method="GET" id="filterForm">

    <!-- LANTAI -->
    <select name="lantai" id="lantaiSelect">
        <option value="">Semua Lantai</option>
        @foreach($lantais as $l)
            <option value="{{ $l->id }}" {{ request('lantai') == $l->id ? 'selected' : '' }}>
                {{ $l->nama_lantai }}
            </option>
        @endforeach
    </select>

    <!-- RUANGAN -->
    <select name="ruangan" id="ruanganSelect" disabled>
        <option value="">Semua Ruangan</option>
        @foreach($ruangans as $r)
            <option value="{{ $r->id }}"
                data-lantai="{{ $r->lantai_id }}"
                {{ request('ruangan') == $r->id ? 'selected' : '' }}>
                {{ $r->nama_ruangan }}
            </option>
        @endforeach
    </select>

        @php
        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        @endphp

        <select name="bulan">
            <option value="">Pilih Bulan</option>
            @foreach($bulanList as $key => $bulan)
                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                    {{ $bulan }}
                </option>
            @endforeach
        </select>

    <!-- TAHUN -->
    <input type="number" name="tahun" placeholder="Tahun" value="{{ request('tahun') }}">

    <button class="btn btn-primary">Filter</button>

    <a href="{{ route('laporan.barang.export', request()->all()) }}" class="btn btn-success">
        Export Excel
    </a>

</form>

    <!-- FILTER HURUF -->
    <div style="margin-bottom:15px;">
        @foreach(range('A','Z') as $h)
            <a href="{{ request()->fullUrlWithQuery(['huruf'=>$h]) }}"
               style="margin:2px; padding:5px 10px; border-radius:6px;
               background: {{ request('huruf')==$h ? '#0066cc':'#eee' }};
               color: {{ request('huruf')==$h ? 'white':'black' }}">
                {{ $h }}
            </a>
        @endforeach
    </div>

    <!-- TABLE -->
    <div class="table-box">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Lokasi</th>
                    <th>Kondisi</th>
                    <th>Riwayat</th>
                </tr>
            </thead>

            <tbody>
                @forelse($barangs as $b)
                <tr>
                    <td>{{ $b->nama_barang }}</td>

                    <td>
                        {{ $b->ruangan->lantai->nama_lantai ?? '-' }} -
                        {{ $b->ruangan->nama_ruangan ?? '-' }}
                    </td>

                    <td>
                        <span class="badge">
                            {{ $b->kondisi_label }}
                        </span>
                    </td>

                    <td>
                        @foreach($b->histories->take(3) as $h)
                            {{ $h->asal->nama_ruangan ?? '-' }}
                            →
                            {{ $h->tujuan->nama_ruangan ?? '-' }}

                            <br>
                            <small>
                                {{ $h->created_at->format('d M Y') }}
                            </small>
                            <br>
                        @endforeach
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" align="center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
const lantai = document.getElementById('lantaiSelect');
const ruangan = document.getElementById('ruanganSelect');

function filterRuangan() {
    let lantaiId = lantai.value;

    if (!lantaiId) {
        ruangan.disabled = true;
        ruangan.value = '';
    } else {
        ruangan.disabled = false;
    }

    Array.from(ruangan.options).forEach(opt => {
        if (!opt.value) return;

        if (!lantaiId || opt.dataset.lantai == lantaiId) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });
}

// jalan saat load & change
window.addEventListener('load', filterRuangan);
lantai.addEventListener('change', filterRuangan);
</script>

@endsection