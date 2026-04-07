@extends('layouts.app')

@section('title', $ruangan->nama_ruangan . ' - SETASET')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/ruangan/show.css') }}">
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a> /
    <a href="{{ route('lantai.show', $ruangan->lantai_id) }}">
    {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}
</a>
</div>

<div class="card">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2>{{ $ruangan->nama_ruangan }}</h2>
        <div class="action-flex">
            @if(Auth::guard('stafaset')->user()->isAdmin())
                <a href="{{ route('ruangan.export', $ruangan->id) }}" class="btn btn-success" target="_blank">📄 Export</a>
            @endif
            <a href="{{ route('pemindahan.laporanpindahbarang') }}" class="btn btn-primary">🔄 Laporan Pindah Barang</a>
            <a href="{{ route('barang.create', $ruangan->id) }}" class="btn btn-primary">+ Tambah Barang</a>
            <a href="{{ route('barang.import.form', $ruangan->id) }}" class="btn btn-primary">⬆️ Import Excel</a>
        </div>
    </div>

    {{-- INFO SECTION WITH CHART --}}
    <div class="info-grid">
        <div class="info-section">
            <p><strong>Lantai:</strong> {{ optional($ruangan->lantai)->nama_lantai ?? '-' }}</p>
            @if($ruangan->penanggungJawab)
                <p><strong>Penanggung Jawab:</strong> {{ $ruangan->penanggungJawab->nama }}</p>
                <p><strong>NIP:</strong> {{ $ruangan->penanggungJawab->nip ?? '-' }}</p>
                <p><strong>Jabatan:</strong> {{ $ruangan->penanggungJawab->jabatan ?? '-' }}</p>
            @endif
            @if($ruangan->keterangan)
                <p><strong>Keterangan:</strong> {{ $ruangan->keterangan }}</p>
            @endif
            <p><strong>Total Barang:</strong> {{ $barangs->total() }} item</p>
        </div>
        <div class="chart-section">
            <h3>📊 Kondisi Barang</h3>
            <canvas id="kondisiChart" style="max-width: 280px; max-height: 280px;"></canvas>
        </div>
    </div>

    {{-- SEARCH BOX --}}
    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Cari barang..." value="{{ request('search') }}">
        </form>
        {{-- Tombol Pilih / Batal --}}
            <button type="button" id="toggleSelectBtn" class="btn btn-success">✓ Pilih</button>
            <button type="button" id="cancelSelectBtn" class="btn btn-secondary" style="display: none;">✕ Batal</button>
    </div>
    

    @if($barangs->count() > 0)

    {{-- FORM UNTUK HAPUS BULK --}}
    <form id="bulkDeleteForm" action="{{ route('barang.bulk.destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="selectedIdsInput" value="">
    </form>

    {{-- BARIS AKSI BULK (muncul saat mode seleksi aktif) --}}
    <div id="bulkActions" class="bulk-actions">
        <div class="selected-info">
            <span>✓</span>
            <span id="selectedCount">0</span> barang dipilih
        </div>
        <button type="button" class="btn btn-danger" id="deleteSelectedBtn">
            🗑️ Hapus Terpilih
        </button>
    </div>

    <div class="table-responsive" id="tableContainer">
        <table class="table">
            <thead>
                <tr>
                    <th class="checkbox-col">
                        <input type="checkbox" id="selectAllCheckbox" class="select-all-checkbox">
                    </th>
                    <th>No</th>
                    <th>Kode</th>
                    <th>
                        {{-- Sorting hanya untuk Nama Barang --}}
                        <a href="{{ request()->fullUrlWithQuery(['direction' => ($direction == 'asc' ? 'desc' : 'asc')]) }}" 
                        class="sort-link">
                            Nama Barang
                            @if($direction == 'asc')
                                &#9650; {{-- panah ke atas (A-Z) --}}
                            @else
                                &#9660; {{-- panah ke bawah (Z-A) --}}
                            @endif
                        </a>
                    </th>
                    <th>Merk/Model</th>
                    <th>No. Seri</th>
                    <th>Ukuran</th>
                    <th>Bahan</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Kondisi</th>
                    <th>Harga</th>
                    <th>Total Nilai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $i => $b)
                <tr>
                    <td class="checkbox-col text-center">
                        <input type="checkbox" name="barang_ids[]" value="{{ $b->id }}" class="barang-checkbox">
                    </td>
                    <td>{{ $barangs->firstItem() + $i }}</td>
                    <td>{{ $b->kode_barang ?? '-' }}</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td>{{ $b->merk_model ?? '-' }}</td>
                    <td>{{ $b->no_seri_pabrik ?? '-' }}</td>
                    <td>{{ $b->ukuran ?? '-' }}</td>
                    <td>{{ $b->bahan ?? '-' }}</td>
                    <td>{{ $b->tahun_pembuatan ?? '-' }}</td>
                    <td class="text-center">{{ $b->jumlah }}</td>
                    <td>
                        @if($b->kondisi === 'B')
                            <span class="badge-kondisi badge-baik">Baik</span>
                        @elseif($b->kondisi === 'KB')
                            <span class="badge-kondisi badge-kurang">Kurang Baik</span>
                        @elseif($b->kondisi === 'RB')
                            <span class="badge-kondisi badge-rusak">Rusak Berat</span>
                        @else - @endif
                    </td>
                    <td class="text-end">
                        @if($b->harga_perolehan && is_numeric($b->harga_perolehan))
                            Rp {{ number_format((float)$b->harga_perolehan, 0, ',', '.') }}
                        @else - @endif
                    </td>
                    <td class="text-end">
                        @if($b->total_nilai && is_numeric($b->total_nilai))
                            Rp {{ number_format((float)$b->total_nilai, 0, ',', '.') }}
                        @else - @endif
                    </td>
                    <td>{{ $b->keterangan ?? '-' }}</td>
                    <td style="white-space: nowrap;">
                        <a href="{{ route('barang.edit', $b->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="var f=this.closest('form'); showConfirm({
                                    title: 'Hapus Barang?',
                                    message: 'Yakin ingin menghapus barang ini? Data tidak dapat dikembalikan.',
                                    type: 'danger',
                                    confirmText: '🗑️ Ya, Hapus',
                                    onConfirm: function() { f.submit(); }
                                })">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($barangs->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan {{ $barangs->firstItem() }} sampai {{ $barangs->lastItem() }} dari {{ $barangs->total() }} entri
        </div>
        <div class="pagination-nav">
            <ul class="pagination">
                @if ($barangs->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">‹</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $barangs->previousPageUrl() }}" rel="prev">‹</a></li>
                @endif

                @php
                    $current = $barangs->currentPage();
                    $last = $barangs->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                    if ($start > 1) echo '<li class="page-item"><a class="page-link" href="'.$barangs->url(1).'">1</a></li>';
                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                @endphp
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $barangs->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endfor
                @php
                    if ($end < $last - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    if ($end < $last) echo '<li class="page-item"><a class="page-link" href="'.$barangs->url($last).'">'.$last.'</a></li>';
                @endphp

                @if ($barangs->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $barangs->nextPageUrl() }}" rel="next">›</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">›</span></li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <h3>Tidak Ada Barang</h3>
        <p>{{ request('search') ? 'Hasil pencarian tidak ditemukan.' : 'Klik tombol "Tambah Barang" untuk memulai.' }}</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    // Chart.js code (sama seperti sebelumnya)
    const barangs = @json($barangs->items());
    let baik = 0, kurangBaik = 0, rusakBerat = 0;
    barangs.forEach(barang => {
        if (barang.kondisi === 'B') baik += parseInt(barang.jumlah);
        else if (barang.kondisi === 'KB') kurangBaik += parseInt(barang.jumlah);
        else if (barang.kondisi === 'RB') rusakBerat += parseInt(barang.jumlah);
    });
    const total = baik + kurangBaik + rusakBerat;
    const ctx = document.getElementById('kondisiChart');
    if (total > 0) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Baik', 'Kurang Baik', 'Rusak Berat'],
                datasets: [{
                    data: [baik, kurangBaik, rusakBerat],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
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
                            padding: 15,
                            font: { size: 12 },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
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
                                const value = context.parsed || 0;
                                const percentage = ((value / total) * 100).toFixed(1);
                                return context.label + ': ' + value + ' item (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    } else {
        ctx.parentElement.innerHTML = '<p style="text-align: center; color: #999; padding: 40px 0;">Belum ada data barang</p>';
    }

    // ---------- SELECT MODE TOGGLE ----------
    const tableContainer = document.getElementById('tableContainer');
    const toggleSelectBtn = document.getElementById('toggleSelectBtn');
    const cancelSelectBtn = document.getElementById('cancelSelectBtn');
    const bulkActionsDiv = document.getElementById('bulkActions');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.barang-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const selectedIdsInput = document.getElementById('selectedIdsInput');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');

    let selectModeActive = false;

    function updateBulkUI() {
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        const count = checkedBoxes.length;
        selectedCountSpan.innerText = count;
        if (count > 0) {
            bulkActionsDiv.style.display = 'flex';
        } else {
            bulkActionsDiv.style.display = 'none';
        }
        // Update hidden input dengan ID yang dipilih
        const ids = checkedBoxes.map(cb => cb.value).join(',');
        selectedIdsInput.value = ids;
    }

    function enableSelectMode() {
        selectModeActive = true;
        tableContainer.classList.add('select-mode');
        toggleSelectBtn.style.display = 'none';
        cancelSelectBtn.style.display = 'inline-flex';
        // Reset all checkboxes to unchecked
        checkboxes.forEach(cb => cb.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkUI(); // will hide bulk actions since none selected
        // Show bulk actions bar only when there is selection, initially hidden
    }

    function disableSelectMode() {
        selectModeActive = false;
        tableContainer.classList.remove('select-mode');
        toggleSelectBtn.style.display = 'inline-flex';
        cancelSelectBtn.style.display = 'none';
        // Hide bulk actions
        bulkActionsDiv.style.display = 'none';
        // Uncheck all checkboxes
        checkboxes.forEach(cb => cb.checked = false);
        if (selectAllCheckbox) selectAllCheckbox.checked = false;
        // Clear selected IDs
        selectedIdsInput.value = '';
    }

    toggleSelectBtn.addEventListener('click', enableSelectMode);
    cancelSelectBtn.addEventListener('click', disableSelectMode);

    // Event listeners untuk checkbox (hanya aktif saat mode seleksi)
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (!selectModeActive) return;
            updateBulkUI();
            if (selectAllCheckbox) {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            if (!selectModeActive) return;
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateBulkUI();
        });
    }

    // Tombol hapus terpilih
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', () => {
            const count = Array.from(checkboxes).filter(cb => cb.checked).length;
            if (count === 0) return;
            showConfirm({
                title: 'Hapus Barang Terpilih?',
                message: `Anda akan menghapus ${count} barang. Data tidak dapat dikembalikan.`,
                type: 'danger',
                confirmText: '🗑️ Ya, Hapus',
                onConfirm: () => {
                    bulkDeleteForm.submit();
                }
            });
        });
    }

    // Inisialisasi: pastikan mode tidak aktif saat halaman dimuat
    disableSelectMode();
</script>
@endsection