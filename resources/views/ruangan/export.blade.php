<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Inventaris - {{ $ruangan->nama_ruangan }}</title>
    <link rel="stylesheet" href="{{ asset('css/ruangan/export.css') }}">

    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 9pt; /* diperkecil dari 10pt */
        margin: 0;
        padding: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* penting agar lebar kolom konsisten */
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 3px;
        vertical-align: top;
        word-wrap: break-word;
    }
    /* Lebar kolom (total 100%) */
    th:nth-child(1), td:nth-child(1) { width: 4%; }  /* No urut */
    th:nth-child(2), td:nth-child(2) { width: 12%; } /* Nama barang */
    th:nth-child(3), td:nth-child(3) { width: 8%; }  /* Merk */
    th:nth-child(4), td:nth-child(4) { width: 6%; }  /* No seri */
    th:nth-child(5), td:nth-child(5) { width: 5%; }  /* Ukuran */
    th:nth-child(6), td:nth-child(6) { width: 5%; }  /* Bahan */
    th:nth-child(7), td:nth-child(7) { width: 6%; }  /* Tahun */
    th:nth-child(8), td:nth-child(8) { width: 8%; }  /* Kode barang */
    th:nth-child(9), td:nth-child(9) { width: 5%; }  /* Jumlah */
    th:nth-child(10), td:nth-child(10) { width: 10%; } /* Harga */
    th:nth-child(11), td:nth-child(11) { width: 4%; } /* B */
    th:nth-child(12), td:nth-child(12) { width: 5%; } /* KB */
    th:nth-child(13), td:nth-child(13) { width: 5%; } /* RB */
    th:nth-child(14), td:nth-child(14) { width: 12%; } /* Keterangan */
    
    .center { text-align: center; }
    .left { text-align: left; }
    .right { text-align: right; }
    .title-row { font-size: 12pt; font-weight: bold; text-align: center; background: #e0e0e0; }
    .table-header { background: #d9d9d9; }
    .info-label { font-weight: bold; width: 100px; }
    .footer {
        margin-top: 15px;
        font-size: 7pt;
        text-align: center;
        border-top: 1px solid #ccc;
        padding-top: 5px;
    }
    @media print {
        .no-print { display: none; }
    }
</style>
</head>
<body>
        @if(empty($pdf))
        <div class="button-group no-print">
            <a href="{{ route('ruangan.pdf', $ruangan->id) }}" class="btn btn-print">
                🖨️ Download PDF
            </a>

            <a href="{{ route('ruangan.export', ['id' => $ruangan->id, 'format' => 'excel']) }}" class="btn btn-excel">
                📊 Export ke Excel
            </a>
        </div>
        @endif

    <table>
        <tr>
            <td colspan="14" class="title-row">KARTU INVENTARIS RUANGAN</td>
        </tr>
        <tr>
            <td class="info-label">KABUPATEN/KOTA</td>
            <td colspan="6">: BANDUNG</td>
            <td colspan="7" rowspan="5" style="text-align: right; vertical-align: top; padding: 5px;">
                NO. KODE LOKASI : {{ $ruangan->kode_lokasi ?? '11.10.00.21.01.25' }}
            </td>
        </tr>
        <tr>
        <td class="label">Tanggal Cetak</td>
        <td colspan="3">: {{ now('Asia/Jakarta')->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">OPD</td>
            <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA</td>
        </tr>
        <tr>
            <td class="info-label">UNIT</td>
            <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA DAERAH PROVINSI JAWA BARAT</td>
        </tr>
        <tr>
            <td class="info-label">RUANGAN</td>
            <td colspan="6">: {{ $ruangan->nama_ruangan }}</td>
        </tr>
        
        <tr class="table-header">
            <th rowspan="2" style="width: 40px;">NO<br>URUT</th>
            <th rowspan="2" style="width: 150px;">NAMA BARANG/JENIS<br>BARANG</th>
            <th rowspan="2" style="width: 100px;">MERK/MODEL</th>
            <th rowspan="2" style="width: 80px;">No. SERI<br>PABRIK</th>
            <th rowspan="2" style="width: 80px;">UKURAN</th>
            <th rowspan="2" style="width: 80px;">BAHAN</th>
            <th rowspan="2" style="width: 80px;">TAHUN<br>PEMBUATAN/PEMB<br>ELIAN</th>
            <th rowspan="2" style="width: 120px;">NO. KODE BARANG</th>
            <th rowspan="2" style="width: 60px;">JUMLAH<br>BARANG/REGIS<br>TER X)</th>
            <th rowspan="2" style="width: 100px;">HARGA<br>BELI/PEROLEHAN<br>(Rp. 000,00)</th>
            <th colspan="3" style="width: 150px;">KEADAAN BARANG</th>
            <th rowspan="2" style="width: 100px;">KETERANGAN<br>MUTASI DLL</th>
        </tr>
        <tr class="table-header">
            <th style="width: 50px;">BAIK<br>(B)</th>
            <th style="width: 50px;">KURANG<br>BAIK (KB)</th>
            <th style="width: 50px;">RUSAK<br>BERAT (RB)</th>
        </tr>
        <tr class="table-header">
            <td class="center">1</td>
            <td class="center">2</td>
            <td class="center">3</td>
            <td class="center">4</td>
            <td class="center">5</td>
            <td class="center">6</td>
            <td class="center">7</td>
            <td class="center">8</td>
            <td class="center">9</td>
            <td class="center">10</td>
            <td class="center">11</td>
            <td class="center">12</td>
            <td class="center">13</td>
            <td class="center">14</td>
        </tr>
        
        @if($ruangan->barangs->count() > 0)
            @foreach($ruangan->barangs as $index => $barang)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="left">{{ $barang->nama_barang }}</td>
                <td class="left">{{ $barang->merk_model ?? '' }}</td>
                <td class="center">{{ $barang->no_seri_pabrik ?? '' }}</td>
                <td class="center">{{ $barang->ukuran ?? '' }}</td>
                <td class="center">{{ $barang->bahan ?? '' }}</td>
                <td class="center">{{ $barang->tahun_pembuatan ?? '' }}</td>
                <td class="center">{{ $barang->kode_barang ?? '' }}</td>
                <td class="center">{{ $barang->jumlah }}</td>
                <td class="right">
                    @php
                        $harga = is_numeric($barang->harga_perolehan) ? floatval($barang->harga_perolehan) : 0;
                    @endphp
                    {{ $harga > 0 ? number_format($harga, 0, ',', '.') : '' }}
                </td>
                <td class="center">{{ $barang->kondisi === 'B' ? '(B)' : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'KB' ? '(KB)' : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'RB' ? '(RB)' : '' }}</td>
                <td class="center">{{ $barang->keterangan }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="14" class="center" style="padding: 30px;">Tidak ada data barang</td>
            </tr>
        @endif
            
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak dari Sistem SETASET - Dinas Komunikasi dan Informatika Kota Bandung</p>
        <p>Tanggal Cetak: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>