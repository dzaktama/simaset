<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $customTitle ?? 'Laporan Peminjaman' }}</title>
    <style>
        /* PENGATURAN KERTAS */
        @page {
            margin: 10mm 15mm 15mm 15mm;
            size: A4 {{ $orientation ?? 'portrait' }};
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
        }
        
        /* HEADER */
        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table td { vertical-align: middle; }
        .header-title h1 { margin: 0; font-size: 16px; text-transform: uppercase; font-weight: 800; }
        .header-title h2 { margin: 2px 0; font-size: 11px; font-weight: bold; color: #555; }
        .header-title p { margin: 0; font-size: 9px; }

        /* INFO BOX */
        .meta-info {
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 9px;
        }
        .meta-table { width: 100%; }
        .meta-table td { padding: 2px 5px; }

        /* TABEL DATA UTAMA */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data th, table.data td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        table.data th {
            background-color: #eee;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            text-align: center;
        }
        
        /* Mencegah baris tabel terpotong saat ganti halaman */
        tr { page-break-inside: avoid; }

        /* STATUS BADGE */
        .badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #ccc;
            text-transform: uppercase;
            display: inline-block;
            text-align: center;
            min-width: 60px;
        }
        .st-approved { background: #dcfce7; color: #166534; border-color: #bbf7d0; } /* Sedang Dipinjam */
        .st-rejected { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .st-returned { background: #e0f2fe; color: #075985; border-color: #bae6fd; }
        .st-pending { background: #fef9c3; color: #854d0e; border-color: #fde047; }

        /* FOOTER & PAGE NUMBER */
        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            border-top: 1px solid #000; padding-top: 5px;
            font-size: 8px; text-align: right; color: #555;
        }
        .page-number:after { content: counter(page); }
        
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="80">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="width: 80px; height: auto;">
                @else
                    <b>LOGO</b>
                @endif
            </td>
            <td class="header-title" style="text-align: center; padding-right: 80px;">
                <h1>{{ $customTitle ?? 'Laporan Riwayat Peminjaman' }}</h1>
                <h2>PT VITECH ASIA - INTEGRATED ASSET MANAGEMENT SYSTEM</h2>
                <p>Dicetak: {{ date('d F Y, H:i') }} WIB | Oleh: {{ auth()->user()->name }}</p>
            </td>
        </tr>
    </table>

    {{-- META INFO --}}
    <div class="meta-info">
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Filter Waktu:</strong></td>
                <td width="35%">{{ $filterDateRange ?? 'Semua Waktu' }}</td>
                <td width="15%"><strong>Filter Status:</strong></td>
                <td width="35%">{{ $filterStatus ?? 'Semua' }}</td>
            </tr>
            <tr>
                <td><strong>Pencarian:</strong></td>
                <td>{{ $search ?: '-' }}</td>
                <td><strong>Total Data:</strong></td>
                <td>{{ count($requests) }} Peminjaman</td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data">
        <thead>
            <tr>
                <th width="20">No</th>
                <th width="70">Tanggal Request</th>
                <th>Peminjam & Departemen</th>
                <th>Aset / Serial Number</th>
                <th width="80">Status</th>
                <th width="80">Jadwal Kembali</th>
                <th width="80">Dikembalikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $index => $req)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                
                {{-- TANGGAL --}}
                <td class="text-center">
                    {{ $req->created_at->format('d/m/Y') }}<br>
                    <span style="color: #666; font-size: 8px;">{{ $req->created_at->format('H:i') }}</span>
                </td>

                {{-- PEMINJAM --}}
                <td>
                    <strong>{{ $req->user->name ?? '-' }}</strong><br>
                    @if($req->user && $req->user->department)
                    <span style="font-size: 8px; color: #555;">Dept: {{ $req->user->department }}</span>
                    @endif
                </td>

                {{-- ASET --}}
                <td>
                    <strong>{{ $req->asset->name ?? '-' }}</strong><br>
                    <span class="font-mono" style="font-size: 9px; color: #555;">{{ $req->asset->serial_number ?? '' }}</span>
                </td>

                {{-- STATUS --}}
                <td class="text-center">
                    @php
                        // Logic Status Badge
                        $statusClass = 'st-pending';
                        $statusLabel = $req->status;

                        if ($req->returned_at) {
                            $statusClass = 'st-returned';
                            $statusLabel = 'Dikembalikan';
                        } elseif ($req->status == 'approved') {
                            $statusClass = 'st-approved';
                            $statusLabel = 'Sedang Dipinjam';
                        } elseif ($req->status == 'rejected') {
                            $statusClass = 'st-rejected';
                            $statusLabel = 'Ditolak';
                        }
                    @endphp
                    <span class="badge {{ $statusClass }}">
                        {{ strtoupper($statusLabel) }}
                    </span>
                </td>

                {{-- JADWAL KEMBALI --}}
                <td class="text-center">
                    @if($req->return_date)
                        {{ $req->return_date->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>

                {{-- DIKEMBALIKAN --}}
                <td class="text-center">
                    @if($req->returned_at)
                        {{ $req->returned_at->format('d/m/Y') }}<br>
                        <span style="color: #666; font-size: 8px;">{{ $req->returned_at->format('H:i') }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    Data tidak ditemukan sesuai filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER NOTE --}}
    @if(isset($adminNotes) && $adminNotes != '-')
    <div style="border: 1px dashed #999; padding: 8px; margin-bottom: 20px; font-size: 9px; background: #fff;">
        <strong>Catatan Tambahan:</strong><br>
        <span style="white-space: pre-line;">{{ $adminNotes }}</span>
    </div>
    @endif

    <div class="footer">
        Dicetak dari Sistem Manajemen Aset &bull; Halaman <span class="page-number"></span>
    </div>

</body>
</html>
