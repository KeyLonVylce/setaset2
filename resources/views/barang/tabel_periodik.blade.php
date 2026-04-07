@extends('layouts.app')

@section('title', 'Tabel Periodik Barang')

@section('styles')
<style>
.header-box {
    background: linear-gradient(135deg,#28a745,#1e7e34);
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

.table-box {
    background:white;
    padding:20px;
    border-radius:12px;
}

.badge {
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.bg-success { background:#d1fae5; color:#065f46; }
.bg-danger { background:#fee2e2; color:#7f1d1d; }
.bg-primary { background:#dbeafe; color:#1e40af; }
</style>
@endsection

@section('content')

<div class="container">

    <!-- HEADER -->
    <div class="header-box">
        <h2>📊 Tabel Periodik Barang</h2>
        <small>Histori aktivitas barang</small>
    </div>

    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a> / tabel periodik barang
    </div>

    <!-- FILTER -->
    <div class="filter-box">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

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
                <option value="">Pilih Ruangan</option>
                @foreach($ruangans as $r)
                    <option value="{{ $r->id }}"
                        data-lantai="{{ $r->lantai_id }}"
                        {{ request('ruangan') == $r->id ? 'selected' : '' }}>
                        {{ $r->nama_ruangan }}
                    </option>
                @endforeach
            </select>

            <!-- BULAN -->
            @php
            $bulanList = [
                1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
            ];
            @endphp

            <select name="bulan">
                <option value="">Bulan</option>
                @foreach($bulanList as $key => $b)
                    <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                        {{ $b }}
                    </option>
                @endforeach
            </select>

            <!-- TAHUN -->
            <input type="number" name="tahun" placeholder="Tahun" value="{{ request('tahun') }}">

            <button class="btn btn-primary">Filter</button>

            <a href="{{ route('laporan.periodik.export', request()->all()) }}" class="btn btn-success">
                Export Excel
            </a>

        </form>
    </div>

    <!-- FILTER HURUF -->
    <div style="margin-bottom:15px;">
        @foreach(range('A','Z') as $h)
            <a href="{{ request()->fullUrlWithQuery(['huruf'=>$h]) }}"
               style="padding:5px 10px; margin:2px;
               background: {{ request('huruf')==$h ? '#28a745':'#eee' }};
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
                    <th>Barang</th>
                    <th>Aktivitas</th>
                    <th>Dari</th>
                    <th>Ke</th>
                    <th>Tanggal</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td>
                        @if(isset($l['pesan']))
                            {!! $l['pesan'] !!}
                        @else
                            {{ $l['barang_nama'] }}
                        @endif
                    </td>

                    <td>
                        @php $akt = $l['aktivitas']; @endphp
                        @if($akt === 'tambah')
                            <span class="badge bg-success">Tambah</span>
                        @elseif($akt === 'hapus')
                            <span class="badge bg-danger">Hapus</span>
                        @elseif($akt === 'edit')
                            <span class="badge bg-warning">Edit</span>
                        @else
                            <span class="badge bg-primary">Pindah</span>
                        @endif
                    </td>

                    <td>{{ $l['dari'] }}</td>
                    <td>{{ $l['ke'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($l['created_at'])->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" align="center">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
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

window.addEventListener('load', filterRuangan);
lantai.addEventListener('change', filterRuangan);
</script>
@endsection