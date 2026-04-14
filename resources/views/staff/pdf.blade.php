<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Staff - SETASET</title>
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

        /* ── Header pakai table agar aman di DomPDF ── */
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #2B5FA3;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: bottom;
        }

        .header-table td.left {
            text-align: left;
            width: 60%;
        }

        .header-table td.right {
            text-align: right;
            width: 40%;
        }

        .title-system {
            font-size: 15px;
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

        /* ── Table ── */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data-table col.col-no    { width: 4%; }
        table.data-table col.col-user  { width: 12%; }
        table.data-table col.col-nama  { width: 16%; }
        table.data-table col.col-nip   { width: 14%; }
        table.data-table col.col-email { width: 24%; }
        table.data-table col.col-role  { width: 12%; }
        table.data-table col.col-date  { width: 18%; }

        table.data-table thead tr {
            background-color: #2B5FA3;
            color: white;
        }

        table.data-table thead th {
            padding: 7px 6px;
            border: 1px solid #1e4a8c;
            font-weight: bold;
            font-size: 10px;
            text-align: left;
            vertical-align: middle;
        }

        table.data-table thead th.center { text-align: center; }

        table.data-table tbody tr.odd  { background-color: #ffffff; }
        table.data-table tbody tr.even { background-color: #f7f7f5; }

        table.data-table tbody td {
            padding: 6px;
            border: 0.5px solid #d0d0d0;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            font-size: 9.5px;
        }

        table.data-table tbody td.center { text-align: center; }

        .nip, .email {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
        }

        /* ── Badge role ── */
        .badge {
            display: inline;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }

        .badge-admin    { background: #EAF3DE; color: #3B6D11; }
        .badge-staff    { background: #E6F1FB; color: #185FA5; }
        .badge-operator { background: #FAEEDA; color: #854F0B; }

        /* ── Summary & Footer ── */
        .summary {
            margin-top: 6px;
            font-size: 8.5px;
            color: #888;
            text-align: right;
        }

        .footer-table {
            width: 100%;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .footer-table td {
            border: none;
            padding: 0;
            padding-top: 8px;
            font-size: 8px;
            color: #888;
            vertical-align: middle;
        }

        .footer-table td.right {
            text-align: right;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td class="left">
                <div class="title-system">SISTEM INFORMASI SETASET</div>
                <div class="title-doc">Daftar Data Staff</div>
            </td>
            <td class="right">
                <div class="header-info">
                    Tanggal cetak: {{ date('d-m-Y') }}<br>
                    Dicetak oleh: {{ auth('stafaset')->user()->nama ?? 'Admin' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data-table">
        <colgroup>
            <col class="col-no">
            <col class="col-user">
            <col class="col-nama">
            <col class="col-nip">
            <col class="col-email">
            <col class="col-role">
            <col class="col-date">
        </colgroup>
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Username</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $index => $staff)
            @php
                $role = strtolower($staff->role);
                $badgeClass = match($role) {
                    'admin'    => 'badge-admin',
                    'operator' => 'badge-operator',
                    default    => 'badge-staff',
                };
            @endphp
            <tr class="{{ $index % 2 === 0 ? 'odd' : 'even' }}">
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $staff->username }}</td>
                <td>{{ $staff->nama }}</td>
                <td class="nip">{{ $staff->nip }}</td>
                <td class="email">{{ $staff->email }}</td>
                <td>
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($staff->role) }}</span>
                </td>
                <td>{{ $staff->created_at->locale('id')->translatedFormat('d F Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">Total: {{ $staffs->count() }} staff terdaftar</div>

    {{-- Footer --}}
    <table class="footer-table">
        <tr>
            <td>Sistem Informasi SETASET &mdash; Dokumen ini dicetak secara otomatis oleh sistem</td>
        </tr>
    </table>

</body>
</html>