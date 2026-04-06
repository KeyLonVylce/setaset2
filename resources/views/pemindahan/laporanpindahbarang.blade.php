@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Laporan Pemindahan Barang</h2>

    {{-- Tombol ke Form Pemindahan --}}
    <a href="{{ route('pindah.form') }}" 
       style="display:inline-block; margin-bottom:15px; padding:10px 15px; background:#28a745; color:white; text-decoration:none; border-radius:5px;">
        + Pindahkan Barang
    </a>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Dari</th>
                <th>Ke</th>
                <th>Catatan</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>{{ $item->jumlah_pindah }}</td>
                <td>{{ $item->asal->nama_ruangan }}</td>
                <td>{{ $item->tujuan->nama_ruangan }}</td>
                <td>{{ $item->notes }}</td>
                <td>{{ $item->created_at->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection