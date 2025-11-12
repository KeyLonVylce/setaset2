@extends('layouts.app')

@section('title', $ruangan->nama_ruangan . ' - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .page-header h2 { font-size: 28px; color: #333; }
    .info-section { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .info-section p { margin: 5px 0; color: #666; }
    .action-buttons { display: flex; gap: 10px; margin-bottom: 20px; }
    .table-responsive { overflow-x: auto; }
    table { min-width: 100%; }
    table th { position: sticky; top: 0; background: #ff9a56; z-index: 10; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .btn-group { display: flex; gap: 5px; }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $ruangan->lantai) }}">{{ $ruangan->lantai }}</a> / 
    {{ $ruangan->nama_ruangan }}
</div>

<div class="card">
    <div class="page-header">
        <h2>{{ $ruangan->nama_ruangan }}</h2>
        <div class="action-buttons">
            <a href="{{ route('ruangan.export', $ruangan->id) }}" class="btn btn-success" target="_blank">📄 Export PDF</a>
            <a href="{{ route('barang.create', $ruangan->id) }}" class="btn btn-primary">+ Tambah Barang</a>
            <a href="{{ route('barang.import.form', $ruangan->id) }}" class="btn btn-warning">⬆️ Import Excel</a>
        </div>
    </div>

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
        <p><strong>Total Barang:</strong> {{ $ruangan->barangs->count() }} item</p>
    </div>

    @if($ruangan->barangs->count() > 0)
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Merk/Model</th>
                    <th>Kode Barang</th>
                    <th>Jumlah</th>
                    <th>Baik</th>
                    <th>Kurang Baik</th>
                    <th>Rusak Berat</th>
                    <th>Harga Perolehan</th>
                    <th>Total Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ruangan->barangs as $index => $barang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->merk_model ?? '-' }}</td>
                    <td>{{ $barang->kode_barang ?? '-' }}</td>
                    <td>{{ $barang->jumlah }}</td>
                    <td>{{ $barang->keadaan_baik }}</td>
                    <td>{{ $barang->keadaan_kurang_baik }}</td>
                    <td>{{ $barang->keadaan_rusak_berat }}</td>
                    <td>Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($barang->total_nilai, 0, ',', '.') }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('barang.edit', $barang->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #f9f9f9;">
                    <td colspan="4">TOTAL</td>
                    <td>{{ $ruangan->barangs->sum('jumlah') }}</td>
                    <td>{{ $ruangan->barangs->sum('keadaan_baik') }}</td>
                    <td>{{ $ruangan->barangs->sum('keadaan_kurang_baik') }}</td>
                    <td>{{ $ruangan->barangs->sum('keadaan_rusak_berat') }}</td>
                    <td colspan="2">Rp {{ number_format($ruangan->barangs->sum('total_nilai'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="empty-state">
        <h3>Belum Ada Barang</h3>
        <p>Klik tombol "Tambah Barang" untuk memulai</p>
    </div>
    @endif
</div>
@endsection