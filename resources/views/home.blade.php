@extends('layouts.app')

@section('title', 'Home - SETASET')

@section('styles')
<style>
    /* Welcome Card */
    .welcome-card { 
        text-align: center; 
        padding: 50px 40px; 
        background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
        color: white; 
        border-radius: 16px; 
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0, 102, 204, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .welcome-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .welcome-card h2 { 
        font-size: 32px; 
        margin-bottom: 10px;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }
    
    .welcome-card p {
        font-size: 16px;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    
    /* Dashboard Stats Grid */
    .dashboard-grid { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 20px; 
        margin-bottom: 30px; 
    }
    
    .chart-container {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0, 102, 204, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .chart-container h3 {
        font-size: 15px;
        color: #1f2937;
        margin-bottom: 20px;
        font-weight: 600;
        text-align: center;
    }
    
    .chart-wrapper {
        width: 200px;
        height: 200px;
    }
    
    .top-items-container {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0, 102, 204, 0.1);
    }
    
    .top-items-container h3 {
        font-size: 15px;
        color: #1f2937;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .bar-chart-wrapper {
        width: 100%;
        height: 250px;
    }
    
    /* Lantai Section */
    .lantai-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .lantai-header h3 { 
        font-size: 24px; 
        color: #1f2937;
        font-weight: 700;
    }
    
    .lantai-header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .lantai-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 20px; 
        margin-top: 20px; 
    }
    
    .lantai-card-wrapper { 
        position: relative; 
    }
    
    .lantai-card { 
        background: white; 
        padding: 30px; 
        border-radius: 16px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 2px solid transparent;
        text-align: center; 
        cursor: pointer; 
        transition: all 0.3s; 
        text-decoration: none; 
        color: #333; 
        display: block;
        position: relative;
        overflow: hidden;
    }
    
    .lantai-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0066cc 0%, #004c99 100%);
        transform: scaleX(0);
        transition: transform 0.3s;
    }
    
    .lantai-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 30px rgba(0, 102, 204, 0.2);
        border-color: #0066cc;
    }
    
    .lantai-card:hover::before {
        transform: scaleX(1);
    }
    
    .lantai-card h4 { 
        font-size: 22px; 
        margin-bottom: 15px; 
        color: #0066cc;
        font-weight: 700;
    }
    
    .lantai-card p { 
        color: #6b7280; 
        font-size: 14px;
        line-height: 1.6;
    }
    
    .lantai-card .badge { 
        display: inline-block; 
        padding: 6px 14px; 
        border-radius: 20px; 
        font-size: 13px; 
        font-weight: 600; 
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0066cc;
        margin: 5px;
        border: 1px solid rgba(0, 102, 204, 0.2);
    }
    
    .lantai-card-actions { 
        position: absolute; 
        top: 15px; 
        right: 15px; 
        display: flex; 
        gap: 8px; 
        z-index: 10; 
    }
    
    .lantai-card-actions button { 
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer; 
        font-size: 16px; 
        padding: 8px; 
        color: #6b7280;
        transition: all 0.3s;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lantai-card-actions button:hover { 
        background: white;
        color: #0066cc;
        border-color: #0066cc;
        transform: scale(1.1);
    }
    
    .empty-state { 
        text-align: center; 
        padding: 80px 20px; 
        color: #9ca3af;
    }
    
    .empty-state h3 {
        color: #6b7280;
        font-size: 20px;
        margin-bottom: 10px;
    }
    
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
        color: #6b7280;
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
        border: 1px solid #e5e7eb; 
        border-radius: 8px;
        color: #6b7280; 
        text-decoration: none; 
        transition: all 0.2s;
        background: white;
        font-size: 14px;
        cursor: pointer;
        font-weight: 500;
    }
    
    .page-link:hover { 
        background: #f3f4f6; 
        border-color: #0066cc;
        color: #0066cc;
    }
    
    .page-item.active .page-link { 
        background: linear-gradient(135deg, #0066cc 0%, #004c99 100%);
        color: white; 
        border-color: #0066cc; 
        font-weight: 600;
        cursor: default;
    }
    
    .page-item.disabled .page-link { 
        color: #d1d5db; 
        cursor: not-allowed; 
        background: #f9fafb;
        border-color: #e5e7eb;
    }
    
    .page-item.disabled .page-link:hover { 
        background: #f9fafb; 
        border-color: #e5e7eb;
        color: #d1d5db;
    }
    
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            justify-content: center;
        }
        .pagination-info {
            width: 100%;
            text-align: center;
        }
        .chart-wrapper {
            width: 150px;
            height: 150px;
        }
        .bar-chart-wrapper {
            height: 300px;
        }
        .lantai-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
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
    <div class="lantai-header">
        <h3>Daftar Lantai</h3>
        <div class="lantai-header-actions">
            <a href="{{ route('pemindahan.pindah') }}" class="btn btn-primary">📦 Pindahkan Barang</a>
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <button class="btn btn-primary" onclick="openAddLantaiModal()">+ Tambah Lantai</button>
            @endif
        </div>
    </div>

    @if($lantais->count() > 0)
    <div class="lantai-grid">
        @foreach($lantais as $lantai)
        <div class="lantai-card-wrapper">
            @if(Auth::guard('stafaset')->user()->isAdmin())
            <div class="lantai-card-actions">
                <button onclick="event.preventDefault(); openEditLantaiModal({{ $lantai->id }}, '{{ addslashes($lantai->nama_lantai) }}', '{{ addslashes($lantai->keterangan ?? '') }}')" title="Edit">✏️</button>
                <form action="{{ route('lantai.destroy', $lantai->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus lantai ini? Semua ruangan dan barang akan ikut terhapus!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus">🗑️</button>
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
        document.getElementById('editLantaiForm').action = '/lantai/' + id;
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