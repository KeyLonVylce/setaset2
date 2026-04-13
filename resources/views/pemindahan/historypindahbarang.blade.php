@extends('layouts.app')

@section('title', 'Laporan Pemindahan Barang - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pemindahan/historypindahbarang.css') }}">
@endsection

@section('content')
<div class="laporan-container">

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;Laporan Pemindahan Barang
</a> 
</div>

    {{-- Toolbar --}}
    <div class="laporan-toolbar-card">
        <div class="toolbar-left">
            <a href="{{ route('home') }}" class="btn btn-dark">
                ← Kembali
            </a>
            <a href="{{ route('pindah.form') }}" class="btn btn-success">
                ✏️ Pindahkan Barang
            </a>
        </div>
        <span class="summary-badge">
            Total transaksi: <strong>{{ $data->total() }}</strong>
        </span>
    </div>

    {{-- Header --}}
    <div class="laporan-header">
        <h1>📦 Laporan Pemindahan Barang</h1>
        <p>Riwayat seluruh transaksi pemindahan barang antar ruangan</p>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- Table card --}}
    <div class="laporan-card">
        @if(count($data) > 0)
            <div class="laporan-table-wrapper">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Ruangan Asal</th>
                            <th>Ruangan Tujuan</th>
                            <th>Catatan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <span class="barang-name">{{ $item->barang->nama_barang }}</span>
                            </td>
                            <td>
                                <span class="badge-jumlah">{{ $item->jumlah_pindah }} unit</span>
                            </td>
                            <td>
                                <span class="room-asal">{{ $item->asal->nama_ruangan }}</span>
                            </td>
                            <td>
                                <span class="room-tujuan">{{ $item->tujuan->nama_ruangan }}</span>
                            </td>
                            <td>
                                @if($item->notes)
                                    <span class="notes-text">{{ $item->notes }}</span>
                                @else
                                    <span class="notes-empty">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="date-text">{{ $item->created_at->format('d M Y') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION (sama persis dengan referensi) --}}
            @if($data->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan {{ $data->firstItem() }} sampai {{ $data->lastItem() }} dari {{ $data->total() }} transaksi
                </div>
                <div class="pagination-nav">
                    <ul class="pagination">
                        {{-- Tombol Previous --}}
                        @if ($data->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">‹</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $data->previousPageUrl() }}" rel="prev">‹</a></li>
                        @endif

                        {{-- Nomor halaman dengan ellipsis --}}
                        @php
                            $current = $data->currentPage();
                            $last = $data->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                            if ($start > 1) echo '<li class="page-item"><a class="page-link" href="'.$data->url(1).'">1</a></li>';
                            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        @endphp
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $current)
                                <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $data->url($page) }}">{{ $page }}</a></li>
                            @endif
                        @endfor
                        @php
                            if ($end < $last - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            if ($end < $last) echo '<li class="page-item"><a class="page-link" href="'.$data->url($last).'">'.$last.'</a></li>';
                        @endphp

                        {{-- Tombol Next --}}
                        @if ($data->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $data->nextPageUrl() }}" rel="next">›</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">›</span></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- End of pagination -->
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Belum ada data pemindahan</h3>
                <p>Data riwayat pemindahan barang akan muncul di sini.</p>
            </div>
        @endif
    </div>

</div>
@endsection