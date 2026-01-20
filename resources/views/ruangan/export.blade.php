<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Inventaris - {{ $ruangan->nama_ruangan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }
        table td, table th { 
            border: 1px solid #000; 
            padding: 8px;
            vertical-align: middle;
        }
        
        .title-row {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .table-header {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }
        
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        
        .signature-row {
            text-align: center;
            padding: 10px;
        }
        
        @media print {
            .no-print { display: none; }
            @page { margin: 20mm; }
        }
        
        .button-group {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        
        .btn-print { background: #ff7b3d; }
        .btn-print:hover { background: #ff6524; }
        .btn-excel { background: #217346; }
        .btn-excel:hover { background: #1a5c37; }
    </style>
</head>
<body>
    <div class="button-group no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Print / Save PDF</button>
        <a href="{{ route('ruangan.export', ['id' => $ruangan->id, 'format' => 'excel']) }}" class="btn btn-excel">📊 Export ke Excel</a>
    </div>

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
                <td class="center">{{ $barang->kondisi === 'B' ? $barang->jumlah : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'KB' ? $barang->jumlah : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'RB' ? $barang->jumlah : '' }}</td>
                <td></td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="14" class="center" style="padding: 30px;">Tidak ada data barang</td>
            </tr>
        @endif
        
        <tr><td colspan="14" style="border: none; height: 10px;"></td></tr>
        <tr>
            <td colspan="7" class="signature-row">
                MENGETAHUI :<br>
                KEPALA BAGIAN TATA USAHA
                <div style="margin-top: 60px; font-weight: bold;">
                    Hj. ASTRIA PRIANTIE, ST.MM<br>
                    NIP. 197111272007012005
                </div>
            </td>
            <td colspan="7" class="signature-row">
                Bandung, {{ date('d F Y') }}<br>
                PENGELOLA BARANG MILIK NEGARA
                <div style="margin-top: 60px; font-weight: bold;">
                    NANDANG SUHERMAN, A.Md<br>
                    NIP. 197411302007011006
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak dari Sistem SETASET - Dinas Komunikasi dan Informatika Kota Bandung</p>
        <p>Tanggal Cetak: {{ now('Asia/Jakarta')->format('d F Y H:i:s') }}</p>
        <p>Tanggal Cetak: {{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>