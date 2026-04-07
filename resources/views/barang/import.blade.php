@extends('layouts.app')

@section('title', 'Import Barang - ' . $ruangan->nama_ruangan)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/barang/import.css') }}">
@endsection

@section('content')
<div class="import-container">
    <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn-back">
        ← Kembali ke Ruangan
    </a>

    <div class="import-header">
        <h3>Import Data Barang</h3>
        <p>Upload file Excel untuk menambah barang ke ruangan <strong>{{ $ruangan->nama_ruangan }}</strong></p>
    </div>

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

    <form action="{{ route('barang.import', $ruangan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
        </div>

        <div class="form-group">
            <a href="{{ asset('template/barang_template.xlsx') }}" class="template-link">
                📄 Unduh Template Excel Contoh
            </a>
        </div>

        <button type="submit" class="btn-import">
            ⬆️ Import Sekarang
        </button>
    </form>
</div>
@endsection