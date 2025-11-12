<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Inventaris - {{ $ruangan->nama_ruangan }}</title>
    <style>
        @page { margin: 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        .header p { font-size: 12px; margin: 3px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; }
        .info-table .label { width: 150px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .signature-section { margin-top: 30px; display: table; width: 100%; }
        .signature-box { display: table-cell; width: 33%; text-align: center; padding: 10px; }
        .signature-box p { margin: 5px 0; }
        .signature-space { height: 60px; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
        @media print {
            .no-print { display: none; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 10px 20px; background: #ff7b3d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .print-button:hover { background: #ff6524; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print / Save PDF</button>

    <div class="header">
        <h1>PEMERINTAH KOTA BANDUNG</h1>
        <h2>DINAS KOMUNIKASI DAN INFORMATIKA</h2>
        <p>Jl. Wastukencana No. 2 Bandung 40117</p>
        <p>Telp: (022) 4204871 | Email: diskominfo@bandung.go.id</p>
    </div>

    <h3 style="text-align: center; margin-bottom: 20px;">KARTU INVENTARIS BARANG</h3>

    <table class="info-table">
        <tr>
            <td class="label">Ruangan</td>
            <td>: {{ $ruangan->nama_ruangan }}</td>
            <td class="label">Lantai</td>
            <td>: {{ $ruangan->lantai }}</td>
        </tr>
        <tr>
            <td class="label">Penanggung Jawab</td>
            <td>: {{ $ruangan->penanggung_jawab ?? '-' }}</td>
            <td class="label">NIP</td>
            <td>: {{ $ruangan->nip_penanggung_jawab ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td colspan="3">: {{ date('d F Y') }}</td>
        </tr>
    </table>

    @if($ruangan->barangs->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Barang</th>
                <th>Merk/Model</th>
                <th>Kode</th>
                <th style="width: 50px;">Jumlah</th>
                <th style="width: 50px;">Baik</th>
                <th style="width: 50px;">Kurang Baik</th>
                <th style="width: 50px;">Rusak Berat</th>
                <th style="width: 100px;">Harga Satuan (Rp)</th>
                <th style="width: 120px;">Total Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ruangan->barangs as $index => $barang)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $barang->nama_barang }}</td>
                <td>{{ $barang->merk_model ?? '-' }}</td>
                <td class="center">{{ $barang->kode_barang ?? '-' }}</td>
                <td class="center">{{ $barang->jumlah }}</td>
                <td class="center">{{ $barang->keadaan_baik }}</td>
                <td class="center">{{ $barang->keadaan_kurang_baik }}</td>
                <td class="center">{{ $barang->keadaan_rusak_berat }}</td>
                <td class="right">{{ number_format($barang->harga_perolehan, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($barang->total_nilai, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="center">TOTAL</td>
                <td class="center">{{ $ruangan->barangs->sum('jumlah') }}</td>
                <td class="center">{{ $ruangan->barangs->sum('keadaan_baik') }}</td>
                <td class="center">{{ $ruangan->barangs->sum('keadaan_kurang_baik') }}</td>
                <td class="center">{{ $ruangan->barangs->sum('keadaan_rusak_berat') }}</td>
                <td colspan="2" class="right">{{ number_format($ruangan->barangs->sum('total_nilai'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <p style="text-align: center; padding: 40px; color: #999;">Tidak ada data barang</p>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Kepala Dinas</strong></p>
            <div class="signature-space"></div>
            <p>____________________</p>
            <p>NIP. </p>
        </div>
        <div class="signature-box">
            <p>Penanggung Jawab Ruangan,</p>
            <p>&nbsp;</p>
            <div class="signature-space"></div>
            <p>____________________</p>
            <p>NIP. {{ $ruangan->nip_penanggung_jawab ?? '' }}</p>
        </div>
        <div class="signature-box">
            <p>Pengelola Barang,</p>
            <p>&nbsp;</p>
            <div class="signature-space"></div>
            <p>____________________</p>
            <p>NIP. </p>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak dari Sistem SETASET - Dinas Komunikasi dan Informatika Kota Bandung</p>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>

    <script>
        // Auto print on page load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>