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
    .action-flex { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-box {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .search-box input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 250px;
    }
    .search-box input:focus {
        outline: none;
        border-color: #ff7b3d;
    }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    table th { position: sticky; top: 0; background: #ff9a56; z-index: 10; padding: 12px 8px; color: white; text-align: left; }
    table td { padding: 10px 8px; border-bottom: 1px solid #e0e0e0; }
    table tbody tr:hover { background: #f9f9f9; }
    .text-center { text-align: center; }
    .text-end { text-align: right; }
    .badge-kondisi { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-baik { background: #d4edda; color: #155724; }
    .badge-kurang { background: #fff3cd; color: #856404; }
    .badge-rusak { background: #f8d7da; color: #721c24; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    
    /* Pagination Styles */
    .pagination-wrapper { 
        display: flex; 
        justify-content: space-between;
        align-items: center;
        margin-top: 30px; 
        padding: 20px 0;
        flex-wrap: wrap;
        gap: 15px;
    }
    .pagination-info {
        color: #666;
        font-size: 14px;
    }
    .pagination-nav {
        display: flex;
    }
    .pagination { 
        display: flex; 
        list-style: none; 
        gap: 5px; 
        padding: 0; 
        margin: 0; 
        align-items: center;
    }
    .page-item { 
        display: inline-block; 
    }
    .page-link { 
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border: 1px solid #ddd; 
        border-radius: 50%;
        color: #666; 
        text-decoration: none; 
        transition: all 0.2s;
        background: white;
        font-size: 14px;
        cursor: pointer;
    }
    .page-link:hover { 
        background: #f5f5f5; 
        border-color: #bbb; 
    }
    .page-item.active .page-link { 
        background: #00a8ff; 
        color: white; 
        border-color: #00a8ff; 
        font-weight: 600;
        cursor: default;
    }
    .page-item.disabled .page-link { 
        color: #ccc; 
        cursor: not-allowed; 
        background: #fafafa;
        border-color: #e5e5e5;
    }
    .page-item.disabled .page-link:hover { 
        background: #fafafa; 
        border-color: #e5e5e5; 
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            justify-content: center;
        }
        .pagination-info {
            width: 100%;
            text-align: center;
        }
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .action-flex {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">{{ $ruangan->lantai }}</a> / 
    {{ $ruangan->nama_ruangan }}
</div>

<div class="card">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2>{{ $ruangan->nama_ruangan }}</h2>
        <div class="action-flex">
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <a href="{{ route('ruangan.export', $ruangan->id) }}" class="btn btn-success" target="_blank">📄 Export PDF</a>
            @endif
            <a href="{{ route('barang.create', $ruangan->id) }}" class="btn btn-primary">+ Tambah Barang</a>
            <a href="{{ route('barang.import.form', $ruangan->id) }}" class="btn btn-warning">⬆️ Import Excel</a>
        </div>
    </div>

    {{-- INFO --}}
    <div class="info-section">
        <p><strong>Lantai:</strong> {{ $ruangan->lantai }}</p>

        @if($ruangan->penanggung_jawab)
        <p><strong>Penanggung Jawab:</strong> {{ $ruangan->penanggung_jawab }}</p>
        @endif

        @if($ruangan->nip_penanggung_jawab)
        <p><strong>NIP:</strong> {{ $ruangan->nip_penanggung_jawab }}</p>
        @endif

        @if($ruangan->keterangan)
        <p><strong>Keterangan:</strong> {{ $ruangan->keterangan }}</p>
        @endif

        <p><strong>Total Barang:</strong> {{ $barangs->total() }} item</p>
    </div>
    

    {{-- SEARCH BOX --}}
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Cari barang..." value="{{ request('search') }}">
        </form>
    </div>

    @if($barangs->count() > 0)

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
                @foreach($barangs as $i => $b)
                <tr>
                    <td>{{ $barangs->firstItem() + $i }}</td>
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
                        @if($b->harga_perolehan && is_numeric($b->harga_perolehan))
                            Rp {{ number_format((float)$b->harga_perolehan, 0, ',', '.') }}
                        @else - @endif
                    </td>

                    <td class="text-end">
                        @if($b->total_nilai && is_numeric($b->total_nilai))
                            Rp {{ number_format((float)$b->total_nilai, 0, ',', '.') }}
                        @else - @endif
                    </td>

                    <td>{{ $b->keterangan ?? '-' }}</td>

                    <td style="white-space: nowrap;">
                        <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus barang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($barangs->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $barangs->firstItem() }} sampai {{ $barangs->lastItem() }} dari {{ $barangs->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($barangs->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $barangs->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach(range(1, $barangs->lastPage()) as $page)
                    @if ($page == $barangs->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $barangs->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($barangs->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $barangs->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>
                @endif

                {{-- Last Page Link --}}
                @if ($barangs->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $barangs->url($barangs->lastPage()) }}">»</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <h3>Tidak Ada Barang</h3>
        <p>{{ request('search') ? 'Hasil pencarian tidak ditemukan.' : 'Klik tombol "Tambah Barang" untuk memulai.' }}</p>
    </div>
    @endif
</div>
@endsection