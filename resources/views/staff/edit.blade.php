@extends('layouts.app')

@section('title', 'Edit Staff - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/staff/edit.css') }}">
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp;/ &nbsp;
    <a href="{{ route('staff.index') }}">Kelola Staff</a> &nbsp;/ &nbsp;
    Edit Staff
</div>

<div class="card">
    <div class="page-header">
        <h2>Edit Staff</h2>
        <p style="color: #666;">{   { $staff->nama }} ({{ $staff->username }})</p>
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