@extends('layouts.app')

@section('title', 'Edit Profil - SETASET')

@section('styles')
<style>
    .breadcrumb {
        margin-bottom: 20px;
        color: #666;
        font-size: 14px;
    }
    .breadcrumb a {
        color: #0066cc;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .profile-header {
        margin-bottom: 25px;
    }
    .profile-header h2 {
        font-size: 24px;
        color: #0066cc;
        margin-bottom: 5px;
    }
    .profile-header p {
        color: #6b7280;
        font-size: 14px;
    }
    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 12px;
    }
    .btn-batal {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Inter', sans-serif;
    }
    .btn-batal:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
        text-decoration: none;
        color: #374151;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
    }
</style>
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
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $staff->username) }}" required>
        </div>

        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $staff->nama) }}" required>
        </div>

        <div class="form-group">
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" value="{{ old('nip', $staff->nip) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $staff->email) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password Baru <span style="font-weight: normal; color: #6b7280;">(kosongkan jika tidak diubah)</span></label>
            <input type="password" id="password" name="password" minlength="6">
            <div class="invalid-feedback" id="passwordError" style="display: none;">Password minimal 6 karakter.</div>
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