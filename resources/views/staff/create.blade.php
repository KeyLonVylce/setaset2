@extends('layouts.app')

@section('title', 'Tambah Staff - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { margin-bottom: 30px; }
    .page-header h2 { font-size: 28px; color: #333; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .form-group-full { grid-column: 1 / -1; }
    .form-actions { display: flex; gap: 10px; margin-top: 20px; }
    .helper-text { font-size: 12px; color: #666; margin-top: 5px; }
    .checkbox-group { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
    .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
    .checkbox-group label { cursor: pointer; margin: 0; }
    
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('staff.index') }}">Kelola Staff</a> / 
    Tambah Staff
</div>

<div class="card">
    <div class="page-header">
        <h2>Tambah Staff Baru</h2>
    </div>

    <form action="{{ route('staff.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="username">Username <span style="color: red;">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: staff01" required>
                <div class="helper-text">Username untuk login</div>
                @error('username')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nip">NIP <span style="color: red;">*</span></label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 199001012020121001" required>
                <div class="helper-text">Nomor Induk Pegawai</div>
                @error('nip')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group form-group-full">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: John Doe" required>
                @error('nama')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span></label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                <div class="helper-text">Minimal 6 karakter</div>
                @error('password')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <!-- Kosong untuk keseimbangan grid -->
            </div>

            <div class="form-group form-group-full">
                <label>Akses Edit</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="can_edit" name="can_edit" value="1" {{ old('can_edit') ? 'checked' : 'checked' }}>
                    <label for="can_edit">Izinkan staff ini untuk mengedit data</label>
                </div>
                <div class="helper-text">Jika dicentang, staff dapat menambah, mengedit, dan menghapus data</div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Simpan Staff</button>
            <a href="{{ route('staff.index') }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection