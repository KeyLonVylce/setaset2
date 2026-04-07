@extends('layouts.app')

@section('title', 'Edit Profil - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / Edit Profil
</div>

<div class="card">
    <div class="profile-header">
        <h2>Edit Profil</h2>
        <p>Perbarui informasi akun Anda</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PUT')

        {{-- Username --}}
        <div class="form-group">
            <label for="username">Username <span style="color: red;">*</span></label>
            <input type="text" id="username" name="username"
                value="{{ old('username', $staff->username) }}"
                class="@error('username') is-invalid @enderror"
                required>
            <div class="helper-text">Username untuk login</div>
            @error('username')
                <div class="helper-text error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nama Lengkap --}}
        <div class="form-group">
            <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
            <input type="text" id="nama" name="nama"
                value="{{ old('nama', $staff->nama) }}"
                class="@error('nama') is-invalid @enderror"
                required>
            @error('nama')
                <div class="helper-text error">{{ $message }}</div>
            @enderror
        </div>

        {{-- NIP --}}
        <div class="form-group">
            <label for="nip">NIP <span style="color: red;">*</span></label>
            <input type="text" id="nip" name="nip"
                value="{{ old('nip', $staff->nip) }}"
                class="@error('nip') is-invalid @enderror"
                required
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <div class="helper-text error" id="nipError" style="display: none;">
                NIP hanya boleh berisi angka.
            </div>
            <div class="helper-text">Nomor Induk Pegawai</div>
            @error('nip')
                <div class="helper-text error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email"
                value="{{ old('email', $staff->email) }}"
                class="@error('email') is-invalid @enderror"
                required>
            @error('email')
                <div class="helper-text error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">Password Baru
                <span style="font-weight: normal; color: #6b7280;">
                    (kosongkan jika tidak diubah)
                </span>
            </label>
            <input type="password" id="password" name="password"
                class="@error('password') is-invalid @enderror"
                minlength="6">
            <div class="helper-text">Minimal 6 karakter (kosongkan jika tidak ingin mengubah)</div>
            @error('password')
                <div class="helper-text error">{{ $message }}</div>
            @enderror
            <div class="helper-text error" id="passwordError" style="display: none;">
                Password minimal 6 karakter.
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            <a href="{{ route('home') }}" class="btn-batal">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password');
        const errorDiv = document.getElementById('passwordError');
        if (password.value.length > 0 && password.value.length < 6) {
            e.preventDefault();
            errorDiv.style.display = 'block';
            password.focus();
        } else {
            errorDiv.style.display = 'none';
        }
    });
</script>
@endsection