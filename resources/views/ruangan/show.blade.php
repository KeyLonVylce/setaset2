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
    table th { position: sticky; top: 0; background: #ff9a56; z-index: 10; white-space: nowrap; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    .btn-group { display: flex; gap: 5px; }
    .badge-kondisi { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-baik { background: #d4edda; color: #155724; }
    .badge-kurang { background: #fff3cd; color: #856404; }
    .badge-rusak { background: #f8d7da; color: #721c24; }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">{{ $ruangan->nama_lantai }}</a> / 
    {{ $ruangan->nama_ruangan }}
</div>

<div class="card">
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

    @if($ruangan->barangs->count() > 0)
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Merk/Model</th>
                    <th>No. Seri</th>
                    <th>Ukuran</th>
                    <th>Bahan</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Harga Satuan</th>
                    <th>Total Nilai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ruangan->barangs as $index => $barang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $barang->kode_barang ?? '-' }}</td>
                    <td><strong>{{ $barang->nama_barang }}</strong></td>
                    <td>{{ $barang->merk_model ?? '-' }}</td>
                    <td>{{ $barang->no_seri_pabrik ?? '-' }}</td>
                    <td>{{ $barang->ukuran ?? '-' }}</td>
                    <td>{{ $barang->bahan ?? '-' }}</td>
                    <td>{{ $barang->tahun_pembuatan ?? '-' }}</td>
                    <td style="text-align: center;">{{ $barang->jumlah }}</td>
                    <td>
                        @if($barang->kondisi === 'B')
                            <span class="badge-kondisi badge-baik">Baik</span>
                        @elseif($barang->kondisi === 'KB')
                            <span class="badge-kondisi badge-kurang">Kurang Baik</span>
                        @elseif($barang->kondisi === 'RB')
                            <span class="badge-kondisi badge-rusak">Rusak Berat</span>
                        @else
                            <span class="badge-kondisi">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($barang->harga_perolehan && $barang->harga_perolehan != '0')
                            Rp {{ number_format($barang->harga_perolehan, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($barang->total_nilai > 0)
                            Rp {{ number_format($barang->total_nilai, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $barang->keterangan ? Str::limit($barang->keterangan, 50) : '-' }}</td>
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
                    <td colspan="8" style="text-align: right;">TOTAL</td>
                    <td style="text-align: center;">{{ $ruangan->barangs->sum('jumlah') }}</td>
                    <td>
                        <span class="badge-kondisi badge-baik">B: {{ $ruangan->barangs->where('kondisi', 'B')->sum('jumlah') }}</span>
                        <span class="badge-kondisi badge-kurang">KB: {{ $ruangan->barangs->where('kondisi', 'KB')->sum('jumlah') }}</span>
                        <span class="badge-kondisi badge-rusak">RB: {{ $ruangan->barangs->where('kondisi', 'RB')->sum('jumlah') }}</span>
                    </td>
                    <td colspan="2" style="text-align: right;">
                        Rp {{ number_format($ruangan->barangs->sum('total_nilai'), 0, ',', '.') }}
                    </td>
                    <td colspan="2"></td>
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