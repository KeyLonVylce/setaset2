@extends('layouts.app')

@section('title', 'Tambah Staff - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #0066cc; text-decoration: none; }
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
            <label for="username">Username<span style="color: red;">*</span></label>
            
            <input type="text" id="username" name="username"
                value="{{ old('username') }}" placeholder="Contoh: staff01"
                class="@error('username') is-invalid @enderror"
                required>
            <div class="helper-text">Username untuk login</div>
            @error('username')
                <div class="helper-text" style="color: red;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nip">NIP<span style="color: red;">*</span></label>
            
            <input type="text" id="nip" name="nip"
                value="{{ old('nip') }}" placeholder="Contoh: 199001012020121001"
                class="@error('nip') is-invalid @enderror"
                required>
            <div class="helper-text">Nomor Induk Pegawai</div>
            @error('nip')
                <div class="helper-text" style="color: red;">
                    {{ $message }}
                </div>
            @enderror
        </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Contoh: John Doe" required>
                @error('nama')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email<span style="color: red;">*</span></label>
                
                <input type="email" id="email" name="email"
                    value="{{ old('email') }}"
                    placeholder="Contoh: johndoe@gmail.com"
                    class="@error('email') is-invalid @enderror"
                    required>

                @error('email')
                    <div class="helper-text" style="color: red;">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password <span style="color: red;">*</span></label>
                <input type="password" id="password" value="staff123" name="password" placeholder="Minimal 6 karakter" required>
                <div class="helper-text">Minimal 6 karakter</div>
                @error('password')
                <div class="helper-text" style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <!-- Kosong untuk keseimbangan grid -->
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Simpan Staff</button>
            <a href="{{ route('staff.index') }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const errorText = document.getElementById('email-error');

    emailInput.addEventListener('input', function () {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (this.value.length > 0 && !emailPattern.test(this.value)) {
            this.style.borderColor = '#ef4444';
            errorText.style.display = 'block';
        } else {
            this.style.borderColor = '#22c55e';
            errorText.style.display = 'none';
        }
    });
});
</script>