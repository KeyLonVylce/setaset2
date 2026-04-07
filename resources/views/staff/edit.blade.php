@extends('layouts.app')

@section('title', 'Edit Staff - SETASET')

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

    /* Style untuk error */
    input.is-invalid, select.is-invalid, textarea.is-invalid {
        border-color: #ef4444 !important;
        background-color: #fff5f5;
    }
    .invalid-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
    input, select, textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: border-color 0.2s;
    }
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #0066cc;
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('staff.index') }}">Kelola Staff</a> / 
    Edit Staff
</div>

<div class="card">
    <div class="page-header">
        <h2>Edit Staff</h2>
        <p style="color: #666;">{{ $staff->nama }} ({{ $staff->username }})</p>
    </div>
    
    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <!-- Username -->
            <div class="form-group">
                <label for="username">Username <span style="color: red;">*</span></label>
                <input type="text" id="username" name="username"
                    value="{{ old('username', $staff->username) }}"
                    class="@error('username') is-invalid @enderror"
                    required>                
                <div class="helper-text">Username untuk login</div>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- NIP (hanya angka) -->
            <div class="form-group">
                <label for="nip">NIP <span style="color: red;">*</span></label>
                <input type="text" id="nip" name="nip"
                    value="{{ old('nip', $staff->nip) }}"
                    inputmode="numeric" pattern="\d*"
                    class="@error('nip') is-invalid @enderror"
                    required>                
                <div class="helper-text">Nomor Induk Pegawai (hanya angka)</div>
                @error('nip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nama -->
            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" 
                    value="{{ old('nama', $staff->nama) }}"
                    class="@error('nama') is-invalid @enderror"
                    required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email"
                    value="{{ old('email', $staff->email) }}"
                    placeholder="Contoh: johndoe@gmail.com"
                    class="@error('email') is-invalid @enderror"
                    required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" 
                    placeholder="Kosongkan jika tidak ingin mengubah"
                    class="@error('password') is-invalid @enderror">
                <div class="helper-text">Kosongkan jika tidak ingin mengubah password</div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <!-- Kosong untuk keseimbangan grid -->
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Update Staff</button>
            <a href="{{ route('staff.index') }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // NIP: hanya angka (hapus semua karakter non-digit)
        const nipInput = document.getElementById('nip');
        if (nipInput) {
            nipInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        }

        // Opsional: validasi email realtime
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('input', function () {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value.length > 0 && !emailPattern.test(this.value)) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#22c55e';
                }
            });
        }

        // Pastikan field dengan error dari server tetap bertanda merah
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.style.borderColor = '#ef4444';
        });
    });
</script>
@endsection