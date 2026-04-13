@extends('layouts.app') 
<!-- Pakai layout utama -->

@section('title', 'Edit Staff - SETASET') 
<!-- Judul halaman -->

@section('styles')
<link rel="stylesheet" href="{{ asset('css/staff/edit.css') }}">
<!-- Load CSS -->
@endsection

@section('content')

<!-- Breadcrumb (navigasi halaman) -->
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / 
    <a href="{{ route('staff.index') }}">Kelola Staff</a> / 
    Edit Staff
</div>

<div class="card">

    <!-- Header halaman -->
    <div class="page-header">
        <h2>Edit Staff</h2>

        <!-- Menampilkan nama + username -->
        <p style="color: #666;">
            {{ $staff->nama }} ({{ $staff->username }})
        </p>
    </div>

    <!-- FORM UPDATE -->
    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
        @csrf 
        <!-- Token keamanan Laravel -->

        @method('PUT') 
        <!-- Karena HTML cuma support GET & POST -->

        <div class="form-grid">

            <!-- USERNAME -->
            <div class="form-group">
                <label>Username *</label>

                <input type="text" name="username"
                    value="{{ old('username', $staff->username) }}"
                    <!-- old() = ambil input sebelumnya kalau error -->
                    class="@error('username') is-invalid @enderror"
                    required>

                <div class="helper-text">Username untuk login</div>

                <!-- Error validation -->
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- NIP -->
            <div class="form-group">
                <label>NIP *</label>

                <input type="text" name="nip"
                    value="{{ old('nip', $staff->nip) }}"
                    inputmode="numeric"
                    pattern="\d*"
                    <!-- hanya angka -->
                    class="@error('nip') is-invalid @enderror"
                    required>

                <div class="helper-text">Hanya angka</div>

                @error('nip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- NAMA -->
            <div class="form-group">
                <label>Nama Lengkap *</label>

                <input type="text" name="nama"
                    value="{{ old('nama', $staff->nama) }}"
                    class="@error('nama') is-invalid @enderror"
                    required>

                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email *</label>

                <input type="email" name="email"
                    value="{{ old('email', $staff->email) }}"
                    placeholder="Contoh: johndoe@gmail.com"
                    class="@error('email') is-invalid @enderror"
                    required>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label>Password Baru</label>

                <input type="password" name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah"
                    class="@error('password') is-invalid @enderror">

                <!-- Info -->
                <div class="helper-text">
                    Kosongkan jika tidak ingin mengubah password
                </div>

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Dummy grid biar layout rapi -->
            <div class="form-group"></div>
        </div>

        <!-- Tombol aksi -->
        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                Update Staff
            </button>

            <a href="{{ route('staff.index') }}" class="btn btn-danger">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===== VALIDASI NIP (ANGKA ONLY) =====
    const nipInput = document.getElementById('nip');

    if (nipInput) {
        nipInput.addEventListener('input', function () {
            // Replace semua selain angka
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // ===== VALIDASI EMAIL REALTIME =====
    const emailInput = document.getElementById('email');

    if (emailInput) {
        emailInput.addEventListener('input', function () {

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (this.value.length > 0 && !emailPattern.test(this.value)) {
                // Kalau salah
                this.style.borderColor = '#ef4444';
            } else {
                // Kalau benar
                this.style.borderColor = '#22c55e';
            }
        });
    }

    // ===== HANDLE ERROR DARI SERVER =====
    document.querySelectorAll('.is-invalid').forEach(field => {
        // Kasih border merah kalau ada error
        field.style.borderColor = '#ef4444';
    });

});
</script>
@endsection