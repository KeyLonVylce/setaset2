@extends('layouts.app')

@section('title', 'Home - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection


@section('content')
<div class="welcome-card">
    <h2>Selamat Datang di SETASET</h2>
    <p>Sistem Inventaris Aset Dinas Komunikasi dan Informatika Provinsi Jawa Barat</p>
</div>

{{-- Dashboard Statistics --}}
<div class="dashboard-grid">
    <div class="chart-container">
        <h3>📊 Distribusi Kondisi Barang</h3>
        <div class="chart-wrapper">
            <canvas id="globalKondisiChart"></canvas>
        </div>
    </div>
    
    <div class="top-items-container">
        <h3>📈 Top 5 Barang Terbanyak</h3>
        <div class="bar-chart-wrapper">
            <canvas id="topBarangsChart"></canvas>
        </div>
    </div>
</div>

<div class="card">
    <div class="lantai-header-actions">

    <a href="{{ route('laporan.periodik') }}" class="btn btn-success">
        📊 Tabel Periodik
    </a>

    <a href="{{ route('pemindahan.laporanpindahbarang') }}" class="btn btn-success">
        🔄 Laporan Pindah Barang
    </a>

    @if(Auth::guard('stafaset')->user()->isAdmin())
        <button class="btn btn-primary" onclick="openAddLantaiModal()">
            + Tambah Lantai
        </button>
    @endif

</div>

    @if($lantais->count() > 0)
    <div class="lantai-grid">
        @foreach($lantais as $lantai)
        <div class="lantai-card-wrapper">
            @if(Auth::guard('stafaset')->user()->isAdmin())
            <div class="lantai-card-actions">
                <button onclick="event.preventDefault(); openEditLantaiModal({{ $lantai->id }}, '{{ addslashes($lantai->nama_lantai) }}', '{{ addslashes($lantai->keterangan ?? '') }}')" title="Edit">✏️</button>
                
                
                <form action="{{ route('lantai.destroy', $lantai->id) }}" method="POST" style="display: inline;">
    @csrf
    @method('DELETE')

    @if($lantai->ruangans_count > 0)
    <button type="button" title="Hapus"
        onclick="showConfirm({
            title: 'Tidak Bisa Hapus Lantai',
            message: 'Lantai <strong>{{ addslashes($lantai->nama_lantai) }}</strong> masih memiliki <strong>{{ $lantai->ruangans_count }} ruangan</strong>.<br><br>Harap hapus semua ruangan terlebih dahulu sebelum menghapus lantai ini.',
            type: 'warning',
            confirmText: '✓ Mengerti',
            showConfirmOnly: true,
            onConfirm: function() {}
        })">🗑️</button>
    @else
    <button type="button" title="Hapus"
        onclick="var f=this.closest('form'); showConfirm({
            title: 'Hapus Lantai?',
            message: 'Yakin ingin menghapus <strong>{{ addslashes($lantai->nama_lantai) }}</strong>?',
            type: 'danger',
            confirmText: '🗑️ Ya, Hapus',
            onConfirm: function() { f.submit(); }
        })">🗑️</button>
    @endif

</form>




            </div>
            @endif
            <a href="{{ route('lantai.show', $lantai->id) }}" class="lantai-card">
                <h4>{{ $lantai->nama_lantai }}</h4>
                <div>
                    <span class="badge">{{ $lantai->ruangans_count }} Ruangan</span>
                </div>
                @if($lantai->keterangan)
                <p style="margin-top: 10px; font-size: 12px;">{{ Str::limit($lantai->keterangan, 50) }}</p>
                @endif
            </a>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($lantais->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $lantais->firstItem() }} sampai {{ $lantais->lastItem() }} dari {{ $lantais->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($lantais->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $lantais->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach(range(1, $lantais->lastPage()) as $page)
                    @if ($page == $lantais->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $lantais->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($lantais->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $lantais->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">›</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    @endif
    @else
    <div class="empty-state">
        <h3>Belum Ada Lantai</h3>
        <p>Klik tombol "Tambah Lantai" untuk memulai</p>
    </div>
    @endif
</div>

@if(Auth::guard('stafaset')->user()->isAdmin())
<!-- Modal Tambah Lantai -->
<div id="addLantaiModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Lantai Baru</h3>
            <span class="close" onclick="closeAddLantaiModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form action="{{ route('lantai.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama_lantai">Nama Lantai <span style="color: red;">*</span></label>
                    <input type="text" id="nama_lantai" name="nama_lantai" placeholder="Contoh: Lantai 1, Lantai 2, Basement" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" placeholder="Keterangan tambahan (opsional)"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Lantai -->
<div id="editLantaiModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Lantai</h3>
            <span class="close" onclick="closeEditLantaiModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editLantaiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit_nama_lantai">Nama Lantai <span style="color: red;">*</span></label>
                    <input type="text" id="edit_nama_lantai" name="nama_lantai" required>
                </div>
                <div class="form-group">
                    <label for="edit_keterangan">Keterangan</label>
                    <textarea id="edit_keterangan" name="keterangan"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Update</button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    // Data kondisi barang global
    const kondisiBaik = {{ $kondisiBaik }};
    const kondisiKurangBaik = {{ $kondisiKurangBaik }};
    const kondisiRusakBerat = {{ $kondisiRusakBerat }};
    const totalBarang = {{ $totalBarang }};
    
    // Create global pie chart dengan warna biru
    const ctx = document.getElementById('globalKondisiChart');
    
    if (totalBarang > 0) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Baik', 'Kurang Baik', 'Rusak Berat'],
                datasets: [{
                    data: [kondisiBaik, kondisiKurangBaik, kondisiRusakBerat],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            font: {
                                size: 11,
                                family: 'Inter'
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / totalBarang) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value.toLocaleString()} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = ((value / totalBarang) * 100).toFixed(1);
                                return label + ': ' + value.toLocaleString() + ' item (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    } else {
        ctx.parentElement.innerHTML = '<p style="text-align: center; color: #9ca3af; padding: 40px 0;">Belum ada data barang</p>';
    }

    // Create top 5 barangs horizontal bar chart
    const topCtx = document.getElementById('topBarangsChart');
    const topBarangsData = @json($topBarangs);
    
    if (topBarangsData.length > 0) {
        const labels = topBarangsData.map(item => item.nama_barang);
        const values = topBarangsData.map(item => item.total);
        
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah',
                    data: values,
                    backgroundColor: [
                        '#0066cc',
                        '#1a75d9',
                        '#3384e0',
                        '#4d93e6',
                        '#66a2ed'
                    ],
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total: ' + context.parsed.x.toLocaleString() + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 11, family: 'Inter' }
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    y: {
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return label.length > 20 ? label.substring(0, 20) + '...' : label;
                            }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    } else {
        topCtx.parentElement.innerHTML = '<p style="text-align: center; color: #9ca3af; padding: 60px 0;">Belum ada data barang</p>';
    }

    // Modal functions
    function openAddLantaiModal() { 
        document.getElementById('addLantaiModal').style.display = 'block'; 
    }
    
    function closeAddLantaiModal() { 
        document.getElementById('addLantaiModal').style.display = 'none'; 
    }
    
    function openEditLantaiModal(id, nama, keterangan) {
        document.getElementById('editLantaiForm').action = '/admin/lantai/' + id; // tambah prefix admin
        document.getElementById('edit_nama_lantai').value = nama;
        document.getElementById('edit_keterangan').value = keterangan || '';
        document.getElementById('editLantaiModal').style.display = 'block';
    }
    
    function closeEditLantaiModal() {
        document.getElementById('editLantaiModal').style.display = 'none';
    }
    
    window.onclick = function(event) { 
        const addModal = document.getElementById('addLantaiModal'); 
        const editModal = document.getElementById('editLantaiModal');
        if (event.target == addModal) { 
            addModal.style.display = 'none'; 
        }
        if (event.target == editModal) {
            editModal.style.display = 'none';
        }
    }
</script>
@endsection