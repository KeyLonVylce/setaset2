@extends('layouts.app')

@section('title', 'Import Barang - ' . $ruangan->nama_ruangan)

@section('styles')
<style>
    .import-container {
        max-width: 600px;
        margin: 50px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 30px;
    }
    h3 { color: #333; margin-bottom: 20px; text-align: center; }
    .btn-back { display: inline-block; margin-bottom: 20px; color: #ff7b3d; text-decoration: none; }
    .btn-back:hover { text-decoration: underline; }
    .alert { margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="import-container">
    <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn-back">← Kembali ke Ruangan</a>
    <h3>Import Data Barang ({{ $ruangan->nama_ruangan }})</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-bottom:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('barang.import', $ruangan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel (.xlsx / .xls)</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
        </div>

        <div class="mb-3">
            <a href="{{ asset('template/barang_template.xlsx') }}" class="text-muted">
                📄 Unduh Template Excel Contoh
            </a>
        </div>

        <button type="submit" class="btn btn-warning w-100">⬆️ Import Sekarang</button>
    </form>
</div>
@endsection
