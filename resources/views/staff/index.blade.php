@extends('layouts.app')

@section('title', 'Kelola Staff - SETASET')

@section('styles')
<style>
    .breadcrumb { margin-bottom: 20px; color: #666; font-size: 14px; }
    .breadcrumb a { color: #ff7b3d; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
    .page-header h2 { font-size: 28px; color: #333; margin: 0; }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    table th { position: sticky; top: 0; background: #ff9a56; z-index: 10; padding: 12px 8px; color: white; text-align: left; }
    table td { padding: 10px 8px; border-bottom: 1px solid #e0e0e0; }
    table tbody tr:hover { background: #f9f9f9; }
    .text-center { text-align: center; }
    .badge { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; }
    .badge-admin { background: #28a745; color: white; }
    .badge-staff { background: #6c757d; color: white; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
    .empty-state { text-align: center; padding: 60px 20px; color: #999; }
    
    /* Pagination Styles */
    .pagination-wrapper { 
        display: flex; 
        justify-content: space-between;
        align-items: center;
        margin-top: 30px; 
        padding: 20px 0;
        flex-wrap: wrap;
        gap: 15px;
    }
    .pagination-info {
        color: #666;
        font-size: 14px;
    }
    .pagination-nav {
        display: flex;
    }
    .pagination { 
        display: flex; 
        list-style: none; 
        gap: 5px; 
        padding: 0; 
        margin: 0; 
        align-items: center;
    }
    .page-item { 
        display: inline-block; 
    }
    .page-link { 
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border: 1px solid #ddd; 
        border-radius: 50%;
        color: #666; 
        text-decoration: none; 
        transition: all 0.2s;
        background: white;
        font-size: 14px;
        cursor: pointer;
    }
    .page-link:hover { 
        background: #f5f5f5; 
        border-color: #bbb; 
    }
    .page-item.active .page-link { 
        background: #00a8ff; 
        color: white; 
        border-color: #00a8ff; 
        font-weight: 600;
        cursor: default;
    }
    .page-item.disabled .page-link { 
        color: #ccc; 
        cursor: not-allowed; 
        background: #fafafa;
        border-color: #e5e5e5;
    }
    .page-item.disabled .page-link:hover { 
        background: #fafafa; 
        border-color: #e5e5e5; 
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / Kelola Staff
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
                    <th>Role</th>
                    <th>Akses Edit</th>
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
                    <td>
                        <span class="badge badge-{{ $staff->role }}">
                            {{ $staff->role_label }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($staff->can_edit)
                            <span class="badge badge-active">✓ Ya</span>
                        @else
                            <span class="badge badge-inactive">✗ Tidak</span>
                        @endif
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