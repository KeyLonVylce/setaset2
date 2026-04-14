@extends('layouts.app') 
<!-- Menggunakan layout utama -->

@section('title', 'Kelola Staff - SETASET') 
<!-- Judul halaman -->

@section('styles')
<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
<!-- Load CSS dari public -->
@endsection

@section('content')

<!-- Breadcrumb (navigasi halaman) -->
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> / Kelola Staff
</div>

<div class="card">

    <!-- Header halaman -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Kelola Staff</h2>

        <div>
            <a href="{{ route('staff.export') }}" class="btn btn-success">
                Export Excel
            </a>
            <a href="{{ route('staff.export.pdf') }}" class="btn btn-success">
                Export PDF
            </a>
            <a href="{{ route('staff.create') }}" class="btn btn-primary">
                + Tambah Staff
            </a>
        </div>
    </div>

    <!-- Cek apakah ada data -->
    @if($staffs->count() > 0)

    <div class="table-responsive">
        <!-- Responsive table (biar bisa scroll di HP) -->

        <table class="table table-bordered">

            <!-- HEADER TABEL -->
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

            <!-- BODY TABEL -->
            <tbody>

                <!-- Loop data staff -->
                @foreach($staffs as $i => $staff)

                <!-- Highlight kalau admin -->
                <tr style="{{ $staff->role === 'admin' ? 'background: #fff3cd;' : '' }}">
                    <!-- Ternary: kalau admin dikasih warna -->

                    <!-- Nomor urut pagination -->
                    <td>{{ $staffs->firstItem() + $i }}</td>

                    <td>{{ $staff->username }}</td>
                    <td>{{ $staff->nama }}</td>
                    <td>{{ $staff->nip }}</td>
                    <td>{{ $staff->email }}</td>

                    <!-- Role -->
                    <td>
                        <span class="badge badge-{{ $staff->role }}">
                            {{ $staff->role_label }}
                        </span>
                        <!-- role_label biasanya accessor -->
                    </td>

                    <!-- Tanggal dibuat -->
                    <td>
                        {{ $staff->created_at->locale('id')->translatedFormat('d F Y') }}
                        <!-- format tanggal pakai Carbon -->
                    </td>

                    <!-- AKSI -->
                    <td style="white-space: nowrap;">

                        <!-- Kalau admin -->
                        @if($staff->role === 'admin')

                            <!-- Disable tombol -->
                            <button class="btn btn-sm btn-primary" disabled>
                                Edit
                            </button>

                            <button class="btn btn-sm btn-danger" disabled>
                                Hapus
                            </button>

                        @else

                            <!-- Edit -->
                            <a href="{{ route('staff.edit', $staff->id) }}" 
                               class="btn btn-sm btn-primary">
                                Edit
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('staff.destroy', $staff->id) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin hapus {{ $staff->nama }}?')">
                                
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>

                        @endif
                    </td>
                </tr>

                @endforeach
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($staffs->hasPages())

    <div class="pagination-wrapper">

        <!-- Info pagination -->
        <div class="pagination-info">
            Menampilkan {{ $staffs->firstItem() }} 
            sampai {{ $staffs->lastItem() }} 
            dari {{ $staffs->total() }} entri
        </div>

        <!-- Navigasi pagination -->
        <div class="pagination-nav">
            <ul class="pagination">

                <!-- Previous -->
                @if ($staffs->onFirstPage())
                    <li class="disabled"><span>‹</span></li>
                @else
                    <li><a href="{{ $staffs->previousPageUrl() }}">‹</a></li>
                @endif

                <!-- Nomor halaman -->
                @foreach(range(1, $staffs->lastPage()) as $page)

                    @if ($page == $staffs->currentPage())
                        <li class="active">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $staffs->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif

                @endforeach

                <!-- Next -->
                @if ($staffs->hasMorePages())
                    <li><a href="{{ $staffs->nextPageUrl() }}">›</a></li>
                @else
                    <li class="disabled"><span>›</span></li>
                @endif

                <!-- Last page -->
                @if ($staffs->hasMorePages())
                    <li>
                        <a href="{{ $staffs->url($staffs->lastPage()) }}">»</a>
                    </li>
                @else
                    <li class="disabled"><span>»</span></li>
                @endif

            </ul>
        </div>
    </div>

    @endif

    @else

    <!-- Kalau tidak ada data -->
    <div class="empty-state">
        <h3>Belum Ada Staff</h3>
        <p>Klik tombol "Tambah Staff" untuk memulai</p>
    </div>

    @endif

</div>
@endsection