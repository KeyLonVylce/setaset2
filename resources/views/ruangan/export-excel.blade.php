<table>
    <tr>
        <td colspan="14" style="text-align: center; font-weight: bold; font-size: 14px;">KARTU INVENTARIS RUANGAN</td>
    </tr>
    <tr>
        <td>KABUPATEN/KOTA</td>
        <td colspan="6">: BANDUNG</td>
        <td colspan="7" rowspan="5" style="text-align: right;">NO. KODE LOKASI : {{ $ruangan->kode_lokasi ?? '11.10.00.21.01.25' }}</td>
    </tr>
    <tr>
        <td>PROVINSI</td>
        <td colspan="6">: JAWA BARAT</td>
    </tr>
    <tr>
        <td>OPD</td>
        <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA</td>
    </tr>
    <tr>
        <td>UNIT</td>
        <td colspan="6">: DINAS KOMUNIKASI DAN INFORMATIKA  DAERAH PROVINSI JAWA BARAT</td>
    </tr>
    <tr>
        <td>RUANGAN</td>
        <td colspan="6">: {{ $ruangan->nama_ruangan }}</td>
    </tr>
    
    <tr style="text-align: center; font-weight: bold;">
        <td rowspan="2">NO<br>URUT</td>
        <td rowspan="2">NAMA BARANG/JENIS<br>BARANG</td>
        <td rowspan="2">MERK/MODEL</td>
        <td rowspan="2">No. SERI<br>PABRIK</td>
        <td rowspan="2">UKURAN</td>
        <td rowspan="2">BAHAN</td>
        <td rowspan="2">TAHUN<br>PEMBUATAN/PEMB<br>ELIAN</td>
        <td rowspan="2">NO. KODE BARANG</td>
        <td rowspan="2">JUMLAH<br>BARANG/REGIS<br>TER X)</td>
        <td rowspan="2">HARGA<br>BELI/PEROLEHAN<br>(Rp. 000,00)</td>
        <td colspan="3">KEADAAN BARANG</td>
        <td rowspan="2">KETERANGAN<br>MUTASI DLL</td>
    </tr>
    <tr style="text-align: center; font-weight: bold;">
        <td>BAIK<br>(B)</td>
        <td>KURANG<br>BAIK (KB)</td>
        <td>RUSAK<br>BERAT (RB)</td>
    </tr>
    <tr style="text-align: center; font-weight: bold;">
        <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td>
    </tr>
    
    @foreach($ruangan->barangs as $index => $barang)
    <tr>
        <td style="text-align: center;">{{ $index + 1 }}</td>
        <td>{{ $barang->nama_barang }}</td>
        <td>{{ $barang->merk_model ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->no_seri_pabrik ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->ukuran ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->bahan ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->tahun_pembuatan ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->kode_barang ?? '' }}</td>
        <td style="text-align: center;">{{ $barang->jumlah }}</td>
        <td style="text-align: right;">{{ is_numeric($barang->harga_perolehan) ? number_format($barang->harga_perolehan, 0, ',', '.') : '' }}</td>
        <td style="text-align: center;">{{ $barang->kondisi === 'B' ? $barang->jumlah : '' }}</td>
        <td style="text-align: center;">{{ $barang->kondisi === 'KB' ? $barang->jumlah : '' }}</td>
        <td style="text-align: center;">{{ $barang->kondisi === 'RB' ? $barang->jumlah : '' }}</td>
        <td></td>
    </tr>
    @endforeach
    
    <tr><td colspan="14"></td></tr>
    <tr>
        <td colspan="7" style="text-align: center;">
            MENGETAHUI :<br>KEPALA BAGIAN TATA USAHA<br><br><br><br>
            Hj. ASTRIA PRIANTIE, ST.MM<br>NIP. 197111272007012005
        </td>
        <td colspan="7" style="text-align: center;">
            Bandung, {{ date('d F Y') }}<br>PENGELOLA BARANG MILIK NEGARA<br><br><br><br>
            NANDANG SUHERMAN, A.Md<br>NIP. 197411302007011006
        </td>
    </tr>
</table>