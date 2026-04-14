@extends('layouts.app')

@section('title', 'Edit Profil - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')

<!-- Breadcrumb-->
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;Edit Profil
</div>

<!-- header -->
<div class="card">
    <div class="profile-header">
        <h2>Edit Profil</h2>
        <p>Perbarui informasi akun Anda</p>
    </div>

    <!-- Flash message -->
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PUT')

        {{-- Username --}}
        <div class="form-group">
            <label for="username">Username <span class="required">*</span></label>
            <input type="text" id="username" name="username"
                value="{{ old('username', $staff->username) }}"
                class="@error('username') is-invalid @enderror"
                required>
            <div class="helper-text">Username untuk login</div>
            @error('username')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Nama Lengkap --}}
        <div class="form-group">
            <label for="nama">Nama Lengkap <span class="required">*</span></label>
            <input type="text" id="nama" name="nama"
                value="{{ old('nama', $staff->nama) }}"
                class="@error('nama') is-invalid @enderror"
                required>
            @error('nama')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- NIP --}}
        <div class="form-group">
            <label for="nip">NIP <span class="required">*</span></label>
            <input type="text" id="nip" name="nip"
                value="{{ old('nip', $staff->nip) }}"
                class="@error('nip') is-invalid @enderror"
                required
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <div class="helper-text">Nomor Induk Pegawai</div>
            @error('nip')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                value="{{ old('email', $staff->email) }}"
                class="@error('email') is-invalid @enderror"
                required>
            @error('email')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Divider Ganti Password --}}
        <div class="password-section">
            <div class="password-section-label">Ganti Kata Sandi</div>
            <div class="password-section-hint">Isi bagian ini hanya jika ingin mengubah kata sandi</div>
        </div>

        {{-- Password Lama --}}
        <div class="form-group" id="passwordLamaGroup">
            <label for="password_lama">Kata Sandi Lama</label>
            <div class="input-password-wrapper">
                <input type="password" id="password_lama" name="password_lama"
                    class="@error('password_lama') is-invalid @enderror"
                    placeholder="Masukkan kata sandi lama">
                <button type="button" class="toggle-password" onclick="togglePassword('password_lama', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password_lama')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        {{-- Password Baru --}}
        <div class="form-group">
            <label for="password">Kata Sandi Baru</label>
            <div class="input-password-wrapper">
                <input type="password" id="password" name="password"
                    class="@error('password') is-invalid @enderror"
                    placeholder="Masukkan kata sandi baru"
                    minlength="6">
                <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <div class="helper-text">Minimal 6 karakter</div>
            @error('password')
                <div class="helper-text error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
            @enderror
            <div class="helper-text error" id="passwordError" style="display:none;">
                <i class="bi bi-exclamation-circle"></i> Password minimal 6 karakter.
            </div>
            <div class="helper-text error" id="passwordLamaError" style="display:none;">
                <i class="bi bi-exclamation-circle"></i> Kata sandi lama wajib diisi jika ingin mengganti password.
            </div>
        </div>

    <!-- tombol simpan -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('home') }}" class="btn-batal">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi untuk toggle visibility password
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Validasi form sebelum submit
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const passwordBaru = document.getElementById('password');
        const passwordLama = document.getElementById('password_lama');
        const errorPanjang = document.getElementById('passwordError');
        const errorLama   = document.getElementById('passwordLamaError');

        // Reset error state
        errorPanjang.style.display = 'none';
        errorLama.style.display    = 'none';
        passwordBaru.classList.remove('is-invalid');
        passwordLama.classList.remove('is-invalid');

        let valid = true;

        // Validasi password baru minimal 6 karakter jika diisi
        if (passwordBaru.value.length > 0 && passwordBaru.value.length < 6) {
            errorPanjang.style.display = 'block';
            passwordBaru.classList.add('is-invalid');
            passwordBaru.focus();
            valid = false;
        }

        if (passwordBaru.value.length >= 6 && passwordLama.value.length === 0) {
            errorLama.style.display = 'block';
            passwordLama.classList.add('is-invalid');
            passwordLama.focus();
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
</script>
@endsection