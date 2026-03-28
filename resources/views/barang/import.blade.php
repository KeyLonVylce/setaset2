@extends('layouts.app')

@section('title', 'Import Barang - ' . $ruangan->nama_ruangan)

@section('styles')
<style>
    .import-container {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        padding: 30px;
        border: 1px solid rgba(0, 102, 204, 0.1);
    }

    .import-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .import-header h3 {
        font-size: 24px;
        color: #0066cc;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .import-header p {
        color: #6b7280;
        font-size: 14px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 20px;
        color: #0066cc;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: #004c99;
        text-decoration: underline;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
    }

    .template-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        text-decoration: none;
        font-size: 13px;
        transition: color 0.2s;
    }

    .template-link:hover {
        color: #0066cc;
    }

    .btn-import {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #0066cc, #004c99);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-import:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
    }
</style>
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