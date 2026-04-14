<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Notifikasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 20px 24px;
        }

        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: bottom;
        }

        .header-table td.left  { width: 60%; text-align: left; }
        .header-table td.right { width: 40%; text-align: right; }

        .title-system {
            font-size: 14px;
            font-weight: bold;
            color: #2B5FA3;
            letter-spacing: 0.3px;
        }

        .title-doc {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 3px;
        }

        .header-info {
            font-size: 8.5px;
            color: #555;
            line-height: 1.8;
        }

        .divider {
            border: none;
            border-top: 2.5px solid #2B5FA3;
            margin: 10px 0 14px;
        }

        /* ── Filter info ── */
        .filter-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .filter-table td {
            border: none;
            padding: 2px 0;
            font-size: 9px;
            color: #555;
            vertical-align: middle;
        }

        .filter-table td.right { text-align: right; }

        .filter-label { color: #888; }

        /* ── Badge role di header ── */
        .badge-role {
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-role-admin    { background: #EAF3DE; color: #3B6D11; }
        .badge-role-staff    { background: #E6F1FB; color: #185FA5; }
        .badge-role-operator { background: #FAEEDA; color: #854F0B; }

        /* ── Data table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data-table col.col-no     { width: 5%; }
        table.data-table col.col-tipe   { width: 12%; }
        table.data-table col.col-aksi   { width: 12%; }
        table.data-table col.col-pesan  { width: 41%; }
        table.data-table col.col-tanggal{ width: 18%; }
        table.data-table col.col-status { width: 12%; }

        table.data-table thead tr {
            background-color: #2B5FA3;
            color: white;
        }

        table.data-table thead th {
            padding: 7px 6px;
            border: 1px solid #1e4a8c;
            font-weight: bold;
            font-size: 9.5px;
            text-align: left;
            vertical-align: middle;
        }

        table.data-table thead th.center { text-align: center; }

        table.data-table tbody tr.odd  { background-color: #ffffff; }
        table.data-table tbody tr.even { background-color: #f7f7f5; }

        table.data-table tbody td {
            padding: 6px;
            border: 0.5px solid #d0d0d0;
            font-size: 9px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
        }

        table.data-table tbody td.center { text-align: center; }

        /* ── Status badge ── */
        .badge {
            display: inline;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-read   { background: #EAF3DE; color: #3B6D11; }
        .badge-unread { background: #FAEEDA; color: #854F0B; }

        /* ── Summary & Footer ── */
        .summary {
            margin-top: 6px;
            font-size: 8.5px;
            color: #888;
            text-align: right;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            border-top: 1px solid #ddd;
        }

        .footer-table td {
            border: none;
            padding-top: 8px;
            font-size: 8px;
            color: #888;
            vertical-align: middle;
        }

        .footer-table td.right { text-align: right; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td class="left">
                <div class="title-system">SISTEM INFORMASI SETASET</div>
                <div class="title-doc">Laporan Notifikasi Aktivitas</div>
            </td>
            <td class="right">
                @php
                    $roleUser = strtolower($user->role);
                    $roleBadgeClass = match($roleUser) {
                        'admin'    => 'badge-role-admin',
                        'operator' => 'badge-role-operator',
                        default    => 'badge-role-staff',
                    };
                    // Format tanggal export tanpa jam
                    $exportDateOnly = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $export_date)->format('d/m/Y');
                @endphp
                <div class="header-info">
                    Tanggal Export: {{ $exportDateOnly }}<br>
                    {{ $user->name }}
                    <span class="badge-role {{ $roleBadgeClass }}">{{ ucfirst($user->role) }}</span>
                </div>
            </td>
        </tr>
    </table>
    <hr class="divider">

    {{-- Filter info: hanya admin yang melihat filter kategori --}}
    <table class="filter-table">
        @if(strtolower($user->role) === 'admin')
        <tr>
            <td>
                <span class="filter-label">Filter Status:</span>
                @if($filter_status == 'read') Sudah Dibaca
                @elseif($filter_status == 'unread') Belum Dibaca
                @else Semua Status
                @endif
            </td>
            <td class="right">
                <span class="filter-label">Filter Kategori:</span>
                @if($filter_type && $filter_type != 'all') {{ ucfirst($filter_type) }}
                @else Semua Kategori
                @endif
            </td>
        </tr>
        @else
        <tr>
            <td colspan="2">
                <span class="filter-label">Filter Status:</span>
                @if($filter_status == 'read') Sudah Dibaca
                @elseif($filter_status == 'unread') Belum Dibaca
                @else Semua Status
                @endif
            </td>
        </tr>
        @endif
    </table>

    {{-- Data Table --}}
    <table class="data-table">
        <colgroup>
            <col class="col-no">
            <col class="col-tipe">
            <col class="col-aksi">
            <col class="col-pesan">
            <col class="col-tanggal">
            <col class="col-status">
        </colgroup>
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Tipe</th>
                <th>Aksi</th>
                <th>Pesan</th>
                <th>Tanggal</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notifications as $index => $notif)
            <tr class="{{ $index % 2 === 0 ? 'odd' : 'even' }}">
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ ucfirst($notif->type) }}</td>
                <td>{{ ucfirst($notif->aksi) }}</td>
                <td>{{ strip_tags($notif->pesan) }}</td>
                <td>{{ $notif->created_at->format('d/m/Y') }}</td>
                <td class="center">
                    @if($notif->is_read)
                        <span class="badge badge-read">Dibaca</span>
                    @else
                        <span class="badge badge-unread">Belum Dibaca</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr class="odd">
                <td colspan="6" class="center" style="padding: 12px; color: #888;">
                    Tidak ada data notifikasi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">Total: {{ $notifications->count() }} notifikasi</div>

    {{-- Footer --}}
    <table class="footer-table">
        <tr>
            <td>Laporan ini digenerate secara otomatis oleh Sistem Informasi SETASET</td>
        </tr>
    </table>

</body>
</html>