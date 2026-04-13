<!DOCTYPE html> <!-- Menandakan dokumen HTML5 -->
<html lang="id"> <!-- Bahasa halaman = Indonesia -->
<head>
    <meta charset="UTF-8"> <!-- Support karakter UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Biar responsive di HP -->

    <title>Kartu Inventaris - {{ $ruangan->nama_ruangan }}</title>
    <!-- Judul tab browser, ambil nama ruangan dari Laravel -->

    <link rel="stylesheet" href="{{ asset('css/ruangan/export.css') }}">
    <!-- asset() = ambil file CSS dari folder public -->
</head>

<body>

    <!-- Kondisi: kalau bukan mode PDF -->
    @if(empty($pdf))

    <div class="button-group no-print">
        <!-- Tombol download PDF -->
        <a href="{{ route('ruangan.pdf', $ruangan->id) }}" class="btn btn-print">
            🖨️ Download PDF
        </a>
        <!-- route() = generate URL dari route Laravel -->

        <!-- Tombol export Excel -->
        <a href="{{ route('ruangan.export', ['id' => $ruangan->id, 'format' => 'excel']) }}" class="btn btn-excel">
            📊 Export ke Excel
        </a>
    </div>

    @endif
    <!-- End kondisi -->

    <table>
        <!-- Judul tabel -->
        <tr>
            <td colspan="14" class="title-row">
                <!-- colspan=14 biar full lebar -->
                KARTU INVENTARIS RUANGAN
            </td>
        </tr>

        <!-- Informasi lokasi -->
        <tr>
            <td class="info-label">KABUPATEN/KOTA</td>
            <td colspan="6">: BANDUNG</td>

            <!-- rowspan=5 supaya turun 5 baris -->
            <td colspan="7" rowspan="5" style="text-align: right; vertical-align: top; padding: 5px;">
                NO. KODE LOKASI : {{ $ruangan->kode_lokasi ?? '11.10.00.21.01.25' }}
                <!-- ?? = default value kalau null -->
            </td>
        </tr>

        <!-- Tanggal cetak -->
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td colspan="3">
                : {{ now('Asia/Jakarta')->format('d F Y') }}
                <!-- now() ambil waktu sekarang -->
                <!-- format() buat format tanggal -->
            </td>
        </tr>

        <!-- OPD -->
        <tr>
            <td class="info-label">OPD</td>
            <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA</td>
        </tr>

        <!-- UNIT -->
        <tr>
            <td class="info-label">UNIT</td>
            <td colspan="6">
                : DINAS KOMUNIKASI DAN INFORMATIKA DAERAH PROVINSI JAWA BARAT
            </td>
        </tr>

        <!-- Nama ruangan -->
        <tr>
            <td class="info-label">RUANGAN</td>
            <td colspan="6">: {{ $ruangan->nama_ruangan }}</td>
        </tr>

        <!-- HEADER TABEL -->
        <tr class="table-header">
            <!-- rowspan=2 karena ada sub header -->
            <th rowspan="2" style="width: 40px;">NO<br>URUT</th>
            <th rowspan="2" style="width: 150px;">NAMA BARANG/JENIS<br>BARANG</th>
            <th rowspan="2" style="width: 100px;">MERK/MODEL</th>
            <th rowspan="2" style="width: 80px;">No. SERI<br>PABRIK</th>
            <th rowspan="2" style="width: 80px;">UKURAN</th>
            <th rowspan="2" style="width: 80px;">BAHAN</th>
            <th rowspan="2" style="width: 80px;">TAHUN<br>PEMBUATAN/PEMBELIAN</th>
            <th rowspan="2" style="width: 120px;">NO. KODE BARANG</th>
            <th rowspan="2" style="width: 60px;">JUMLAH<br>BARANG/REGISTER</th>
            <th rowspan="2" style="width: 100px;">HARGA<br>BELI/PEROLEHAN</th>

            <!-- Header gabungan -->
            <th colspan="3" style="width: 150px;">KEADAAN BARANG</th>

            <th rowspan="2" style="width: 100px;">KETERANGAN MUTASI DLL</th>
        </tr>

        <!-- Sub header kondisi -->
        <tr class="table-header">
            <th>BAIK (B)</th>
            <th>KURANG BAIK (KB)</th>
            <th>RUSAK BERAT (RB)</th>
        </tr>

        <!-- Nomor kolom -->
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

        <!-- CEK apakah ada data -->
        @if($ruangan->barangs->count() > 0)

            <!-- LOOP data -->
            @foreach($ruangan->barangs as $index => $barang)

            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <!-- Nomor urut -->

                <td class="left">{{ $barang->nama_barang }}</td>

                <td class="left">{{ $barang->merk_model ?? '' }}</td>
                <!-- Kalau null, kosong -->

                <td class="center">{{ $barang->no_seri_pabrik ?? '' }}</td>
                <td class="center">{{ $barang->ukuran ?? '' }}</td>
                <td class="center">{{ $barang->bahan ?? '' }}</td>
                <td class="center">{{ $barang->tahun_pembuatan ?? '' }}</td>
                <td class="center">{{ $barang->kode_barang ?? '' }}</td>

                <td class="center">{{ $barang->jumlah }}</td>

                <!-- Harga -->
                <td class="right">
                    @php
                        // Cek apakah angka
                        $harga = is_numeric($barang->harga_perolehan) 
                            ? floatval($barang->harga_perolehan) 
                            : 0;
                    @endphp

                    {{ $harga > 0 ? number_format($harga, 0, ',', '.') : '' }}
                    <!-- Format ke rupiah -->
                </td>

                <!-- Kondisi -->
                <td class="center">{{ $barang->kondisi === 'B' ? '(B)' : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'KB' ? '(KB)' : '' }}</td>
                <td class="center">{{ $barang->kondisi === 'RB' ? '(RB)' : '' }}</td>

                <!-- Keterangan -->
                <td class="center">{{ $barang->keterangan }}</td>
            </tr>

            @endforeach
            <!-- End loop -->

        @else
            <!-- Kalau tidak ada data -->
            <tr>
                <td colspan="14" class="center" style="padding: 30px;">
                    Tidak ada data barang
                </td>
            </tr>
        @endif

    </table>

    <!-- Footer -->
    <div class="footer">
        <p>
            Dokumen ini dicetak dari Sistem SETASET - 
            Dinas Komunikasi dan Informatika Kota Bandung
        </p>

        <p>
            Tanggal Cetak: 
            {{ now()->setTimezone('Asia/Jakarta')->format('d F Y H:i:s') }}
            <!-- Format tanggal + jam -->
        </p>
    </div>

</body>
</html>