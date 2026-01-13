@extends('layouts.app')

@section('title', $ruangan->nama_ruangan . ' - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px; flex-wrap: wrap; }
    .page-header h2 { font-size: 28px; color: #333; }
    .info-section { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .info-section p { margin: 5px 0; color: #666; }
    .action-flex { display: flex; gap: 10px; align-items: center; }
    .search-box {
        display: flex;
        gap: 10px;
    }
    .search-box input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 230px;
    }
    .table-responsive { overflow-x: auto; }
    table th { position: sticky; top: 0; background: #ff9a56; z-index: 10; }
    .badge-kondisi { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-baik { background: #d4edda; color: #155724; }
    .badge-kurang { background: #fff3cd; color: #856404; }
    .badge-rusak { background: #f8d7da; color: #721c24; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">{{ $ruangan->nama_lantai }}</a> / 
    {{ $ruangan->nama_ruangan }}
</div>

<div class="card">

    {{-- PAGE HEADER + SEARCH --}}
    <div class="page-header">
        <h2>{{ $ruangan->nama_ruangan }}</h2>
        <div class="action-buttons">
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <a href="{{ route('ruangan.export', $ruangan->id) }}" class="btn btn-success" target="_blank">📄 Export PDF</a>
            @endif
            <a href="{{ route('barang.create', $ruangan->id) }}" class="btn btn-primary">+ Tambah Barang</a>
            <a href="{{ route('barang.import.form', $ruangan->id) }}" class="btn btn-warning">⬆️ Import Excel</a>
        </div>
    </div>

    {{-- INFO --}}
    <div class="info-section">
        <p><strong>Lantai:</strong> {{ $ruangan->nama_lantai }}</p>

        @if($ruangan->penanggung_jawab)
        <p><strong>Penanggung Jawab:</strong> {{ $ruangan->penanggung_jawab }}</p>
        @endif

        @if($ruangan->nip_penanggung_jawab)
        <p><strong>NIP:</strong> {{ $ruangan->nip_penanggung_jawab }}</p>
        @endif

        @if($ruangan->keterangan)
        <p><strong>Keterangan:</strong> {{ $ruangan->keterangan }}</p>
        @endif

        <p><strong>Total Barang:</strong> {{ $ruangan->barangs->count() }} item ({{ $ruangan->barangs->sum('jumlah') }} unit)</p>
    </div>

    {{-- FILTER DATA --}}
    @php
        $search = request('search');
        $filtered = $ruangan->barangs;

        if ($search) {
            $filtered = $filtered->filter(function($b) use ($search) {
                $s = strtolower($search);
                return str_contains(strtolower($b->nama_barang), $s)
                    || str_contains(strtolower($b->kode_barang ?? ''), $s)
                    || str_contains(strtolower($b->merk_model ?? ''), $s)
                    || str_contains(strtolower($b->keterangan ?? ''), $s);
            });
        }
    @endphp

    @if($filtered->count() > 0)

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Merk/Model</th>
                    <th>No. Seri</th>
                    <th>Ukuran</th>
                    <th>Bahan</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Harga</th>
                    <th>Total Nilai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($filtered as $i => $b)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $b->kode_barang ?? '-' }}</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td>{{ $b->merk_model ?? '-' }}</td>
                    <td>{{ $b->no_seri_pabrik ?? '-' }}</td>
                    <td>{{ $b->ukuran ?? '-' }}</td>
                    <td>{{ $b->bahan ?? '-' }}</td>
                    <td>{{ $b->tahun_pembuatan ?? '-' }}</td>
                    <td class="text-center">{{ $b->jumlah }}</td>

                    <td>
                        @if($b->kondisi === 'B') <span class="badge-baik badge-kondisi">Baik</span>
                        @elseif($b->kondisi === 'KB') <span class="badge-kurang badge-kondisi">Kurang Baik</span>
                        @elseif($b->kondisi === 'RB') <span class="badge-rusak badge-kondisi">Rusak Berat</span>
                        @else - @endif
                    </td>

                    <td class="text-end">
                        @if($b->harga_perolehan)
                            Rp {{ number_format($b->harga_perolehan, 0, ',', '.') }}
                        @else - @endif
                    </td>

                    <td class="text-end">
                        @if($b->total_nilai)
                            Rp {{ number_format($b->total_nilai, 0, ',', '.') }}
                        @else - @endif
                    </td>

                    <td>{{ $b->keterangan ?? '-' }}</td>

                    <td>
                        <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr style="font-weight: bold; background: #f9f9f9;">
                    <td colspan="8" class="text-end">TOTAL</td>
                    <td class="text-center">{{ $filtered->sum('jumlah') }}</td>

                    <td>
                        <span class="badge-kondisi badge-baik">B: {{ $filtered->where('kondisi', 'B')->sum('jumlah') }}</span>
                        <span class="badge-kondisi badge-kurang">KB: {{ $filtered->where('kondisi', 'KB')->sum('jumlah') }}</span>
                        <span class="badge-kondisi badge-rusak">RB: {{ $filtered->where('kondisi', 'RB')->sum('jumlah') }}</span>
                    </td>

                    <td colspan="2" class="text-end">
                        Rp {{ number_format($filtered->sum('total_nilai'), 0, ',', '.') }}
                    </td>

                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @else
    <div class="empty-state">
        <h3>Tidak Ada Barang</h3>
        <p>Hasil pencarian tidak ditemukan.</p>
    </div>
    @endif
</div>
@endsection
