@extends('layouts.app')

@section('title', 'Import Barang - ' . $ruangan->nama_ruangan)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/import.css') }}">
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">
    {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
</a> &nbsp;/ &nbsp;
    <a href="{{ route('ruangan.show', $ruangan->id) }}">{{ $ruangan->nama_ruangan }}</a> &nbsp;/&nbsp;
    Import Barang
</div>

<!-- tombol kembali ke ruangan -->
<div class="import-container">
    <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn-back">
        ← Kembali ke Ruangan
    </a>

    <!-- Header dan deskripsi import barang -->
    <div class="import-header">
        <h3>Import Data Barang</h3>
        <p>Upload file Excel untuk menambah barang ke ruangan <strong>{{ $ruangan->nama_ruangan }}</strong></p>
    </div>

    <!-- Tampilkan pesan sukses atau error -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form untuk upload file Excel -->
    <form action="{{ route('barang.import', $ruangan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
        </div>

        <!-- Link untuk download template Excel -->
        <div class="form-group">
            <a href="{{ asset('template/barang_template.xlsx') }}" class="template-link">
                📄 Unduh Template Excel Contoh
            </a>
        </div>

        <!-- Tombol submit untuk import barang -->
        <button type="submit" class="btn-import">
            ⬆️ Import Sekarang
        </button>
    </form>
</div>
@endsection