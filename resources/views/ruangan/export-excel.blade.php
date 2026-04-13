<table> <!-- Tag utama untuk membuat tabel -->

    <!-- Judul tabel -->
    <tr> <!-- Baris -->
        <td colspan="14" style="text-align: center; font-weight: bold; font-size: 14px;">
            <!-- colspan="14" artinya cell ini gabung 14 kolom -->
            KARTU INVENTARIS RUANGAN
        </td>
    </tr>

    <!-- Baris informasi kabupaten/kota -->
    <tr>
        <td>KABUPATEN/KOTA</td> <!-- Label -->
        <td colspan="6">: BANDUNG</td> <!-- Isi data, digabung 6 kolom -->

        <!-- rowspan="5" artinya cell ini turun ke bawah 5 baris -->
        <td colspan="7" rowspan="5" style="text-align: right;">
            NO. KODE LOKASI : {{ $ruangan->kode_lokasi ?? '11.10.00.21.01.25' }}
            <!-- {{ }} untuk output data Laravel -->
            <!-- ?? untuk default value kalau null -->
        </td>
    </tr>

    <!-- Baris provinsi -->
    <tr>
        <td>PROVINSI</td>
        <td colspan="6">: JAWA BARAT</td>
    </tr>

    <!-- Baris OPD -->
    <tr>
        <td>OPD</td>
        <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA</td>
    </tr>

    <!-- Baris unit -->
    <tr>
        <td>UNIT</td>
        <td colspan="6">
            : DINAS KOMUNIKASI DAN INFORMATIKA DAERAH PROVINSI JAWA BARAT
        </td>
    </tr>

    <!-- Baris nama ruangan -->
    <tr>
        <td>RUANGAN</td>
        <td colspan="6">: {{ $ruangan->nama_ruangan }}</td>
        <!-- Mengambil data nama ruangan dari database -->
    </tr>

    <!-- Header utama tabel -->
    <tr style="text-align: center; font-weight: bold;">
        <td rowspan="2">NO<br>URUT</td> <!-- rowspan=2 karena ada sub header -->
        <td rowspan="2">NAMA BARANG/JENIS<br>BARANG</td>
        <td rowspan="2">MERK/MODEL</td>
        <td rowspan="2">No. SERI<br>PABRIK</td>
        <td rowspan="2">UKURAN</td>
        <td rowspan="2">BAHAN</td>
        <td rowspan="2">TAHUN<br>PEMBUATAN/PEMBELIAN</td>
        <td rowspan="2">NO. KODE BARANG</td>
        <td rowspan="2">JUMLAH<br>BARANG/REGISTER</td>
        <td rowspan="2">HARGA<br>BELI/PEROLEHAN</td>

        <!-- Header gabungan -->
        <td colspan="3">KEADAAN BARANG</td>

        <td rowspan="2">KETERANGAN MUTASI DLL</td>
    </tr>

    <!-- Sub header kondisi -->
    <tr style="text-align: center; font-weight: bold;">
        <td>BAIK (B)</td>
        <td>KURANG BAIK (KB)</td>
        <td>RUSAK BERAT (RB)</td>
    </tr>

    <!-- Nomor kolom (biasanya buat referensi form resmi) -->
    <tr style="text-align: center; font-weight: bold;">
        <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
        <td>7</td><td>8</td><td>9</td><td>10</td>
        <td>11</td><td>12</td><td>13</td><td>14</td>
    </tr>

    <!-- Loop data barang -->
    @foreach($ruangan->barangs as $index => $barang)
    <!-- $ruangan->barangs = relasi data barang -->
    <!-- $index = nomor urut, $barang = isi data -->

    <tr>
        <!-- Nomor urut -->
        <td style="text-align: center;">{{ $index + 1 }}</td>

        <!-- Nama barang -->
        <td>{{ $barang->nama_barang }}</td>

        <!-- Merk, kalau null tampil kosong -->
        <td>{{ $barang->merk_model ?? '' }}</td>

        <!-- Nomor seri -->
        <td style="text-align: center;">
            {{ $barang->no_seri_pabrik ?? '' }}
        </td>

        <!-- Ukuran -->
        <td style="text-align: center;">
            {{ $barang->ukuran ?? '' }}
        </td>

        <!-- Bahan -->
        <td style="text-align: center;">
            {{ $barang->bahan ?? '' }}
        </td>

        <!-- Tahun -->
        <td style="text-align: center;">
            {{ $barang->tahun_pembuatan ?? '' }}
        </td>

        <!-- Kode barang -->
        <td style="text-align: center;">
            {{ $barang->kode_barang ?? '' }}
        </td>

        <!-- Jumlah -->
        <td style="text-align: center;">
            {{ $barang->jumlah }}
        </td>

        <!-- Harga dengan format ribuan -->
        <td style="text-align: right;">
            {{ is_numeric($barang->harga_perolehan) 
                ? number_format($barang->harga_perolehan, 0, ',', '.') 
                : '' }}
            <!-- number_format buat format Rp -->
        </td>

        <!-- Kondisi barang -->
        <td style="text-align: center;">
            {{ $barang->kondisi === 'B' ? '(B)' : '' }}
            <!-- Ternary: kalau kondisi B tampilkan -->
        </td>

        <td style="text-align: center;">
            {{ $barang->kondisi === 'KB' ? '(KB)' : '' }}
        </td>

        <td style="text-align: center;">
            {{ $barang->kondisi === 'RB' ? '(RB)' : '' }}
        </td>

        <!-- Keterangan tambahan -->
        <td></td>
    </tr>
    @endforeach
    <!-- End loop -->

    <!-- Spacer kosong -->
    <tr>
        <td colspan="14"></td>
    </tr>

    <!-- Tanda tangan -->
    <tr>
        <td colspan="7" style="text-align: center;">
            MENGETAHUI :<br>
            KEPALA BAGIAN TATA USAHA<br><br><br><br>
            Hj. ASTRIA PRIANTIE, ST.MM<br>
            NIP. 197111272007012005
        </td>

        <td colspan="7" style="text-align: center;">
            Bandung, {{ date('d F Y') }}<br>
            <!-- date() untuk tanggal sekarang -->
            PENGELOLA BARANG MILIK NEGARA<br><br><br><br>
            NANDANG SUHERMAN, A.Md<br>
            NIP. 197411302007011006
        </td>
    </tr>

</table>