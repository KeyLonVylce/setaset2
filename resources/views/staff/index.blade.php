@extends('layouts.app')

@section('title', 'Kelola Staff - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home </a> &nbsp; / &nbsp; Kelola Staff
</div>

<div class="card">
    <div class="page-header">
        <h2>Kelola Staff</h2>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">+ Tambah Staff</a>
    </div>

    @if($staffs->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($staffs as $i => $staff)
                <tr style="{{ $staff->role === 'admin' ? 'background: #fff3cd;' : '' }}">
                    <td>{{ $staffs->firstItem() + $i }}</td>
                    <td>{{ $staff->username }}</td>
                    <td>{{ $staff->nama }}</td>
                    <td>{{ $staff->nip }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <span class="badge badge-{{ $staff->role }}">
                            {{ $staff->role_label }}
                        </span>
                    </td>
                    <td>{{ $staff->created_at->format('d M Y') }}</td>
                    <td style="white-space: nowrap;">
                        @if($staff->role === 'admin')
                            <button class="btn btn-sm btn-primary" disabled title="Tidak dapat mengedit Administrator">Edit</button>
                            <button class="btn btn-sm btn-danger" disabled title="Tidak dapat menghapus Administrator">Hapus</button>
                        @else
                            <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus akun {{ $staff->nama }}?')">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($staffs->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $staffs->firstItem() }} sampai {{ $staffs->lastItem() }} dari {{ $staffs->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                @if ($staffs->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $staffs->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                @foreach(range(1, $staffs->lastPage()) as $page)
                    @if ($page == $staffs->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $staffs->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if ($staffs->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $staffs->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>
                @endif

                @if ($staffs->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $staffs->url($staffs->lastPage()) }}">»</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">»</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <h3>Belum Ada Staff</h3>
        <p>Klik tombol "Tambah Staff" untuk memulai</p>
    </div>
    @endif
</div>
@endsection