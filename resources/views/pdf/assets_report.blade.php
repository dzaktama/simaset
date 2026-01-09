<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        
        /* HEADER STYLE BARU (Logo Besar + Teks) */
        .header-table { width: 100%; border-bottom: 3px double #444; margin-bottom: 20px; padding-bottom: 10px; }
        .logo-container { width: 120px; text-align: left; vertical-align: middle; }
        /* Logo diperbesar disini (height: 75px) */
        .logo-img { height: 75px; width: auto; object-fit: contain; } 
        .title-container { text-align: center; vertical-align: middle; padding-right: 120px; /* Biar center relatif ke halaman */ }
        .title-container h1 { margin: 0; font-size: 18pt; text-transform: uppercase; color: #222; }
        .title-container p { margin: 5px 0; font-size: 10pt; color: #666; }

        /* Meta Info Style */
        .meta-info { margin-bottom: 15px; font-size: 9pt; background: #f8f9fa; padding: 10px; border: 1px solid #eee; }
        .meta-item { margin-bottom: 4px; }
        
        /* Badge Colors */
        .badge { padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8pt; color: #fff; display: inline-block; }
        .bg-green { background-color: #10b981; } .bg-blue { background-color: #3b82f6; } .bg-red { background-color: #ef4444; } .bg-gray { background-color: #6b7280; }

        /* Main Table */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        table.data-table th { bg-color: #2d3748; background-color: #2d3748; color: white; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        table.data-table tr:nth-child(even) { background-color: #f2f2f2; }
        
        .img-cell { width: 60px; text-align: center; }
        .img-cell img { width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc; padding: 2px; background: #fff; }
        
        .footer { margin-top: 30px; font-size: 9pt; page-break-inside: avoid; }
        .notes { border: 1px dashed #aaa; padding: 10px; margin-bottom: 20px; background: #fffbeb; }
        .signature { text-align: right; margin-top: 40px; margin-right: 20px; }
    </style>
</head>
<body>
    
    {{-- REVISI HEADER: Pakai Tabel agar Logo Rapi & Besar --}}
    <table class="header-table">
        <tr>
            <td class="logo-container">
                {{-- Menggunakan public_path agar terbaca oleh DomPDF --}}
                <img src="{{ public_path('img/logoVitechAsia.png') }}" class="logo-img">
            </td>
            <td class="title-container">
                <h1>{{ $title }}</h1>
                <p>Laporan Inventaris & Audit Aset IT</p>
            </td>
        </tr>
    </table>

    <div class="meta-info">
        <div class="meta-item"><strong>Waktu Cetak:</strong> {{ $printTime }}</div>
        <div class="meta-item"><strong>Filter Status:</strong> {{ ucfirst($filterStatus) }}</div>
        
        @if($filterSearch)
            <div class="meta-item" style="color: #d97706;">
                <strong>Filter Pencarian:</strong> "<i>{{ $filterSearch }}</i>"
            </div>
        @endif
        
        <div class="meta-item"><strong>Total Data:</strong> {{ count($assets) }} Aset</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                @if($showImages) <th style="width: 10%; text-align: center;">Foto</th> @endif
                <th style="width: 25%;">Nama Aset / SN</th>
                <th style="width: 10%; text-align: center;">Stok</th>
                <th style="width: 15%;">Status</th>
                <th>Lokasi / Peminjam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                
                @if($showImages)
                <td class="img-cell">
                    @if($asset->image)
                        <img src="{{ public_path('storage/' . $asset->image) }}">
                    @else
                        <span style="color: #ccc; font-size: 8pt;">-</span>
                    @endif
                </td>
                @endif
                
                <td>
                    <div style="font-weight: bold;">{{ $asset->name }}</div>
                    <div style="font-family: monospace; color: #555; font-size: 8pt;">{{ $asset->serial_number }}</div>
                </td>
                <td style="text-align: center;">{{ $asset->quantity }}</td>
                <td>
                    @php
                        $color = match($asset->status) {
                            'available' => 'bg-green',
                            'deployed' => 'bg-blue',
                            'broken', 'maintenance' => 'bg-red',
                            default => 'bg-gray'
                        };
                    @endphp
                    <span class="badge {{ $color }}">{{ ucfirst($asset->status) }}</span>
                </td>
                <td>
                    @if($asset->status == 'deployed' && $asset->holder)
                        <strong>{{ $asset->holder->name }}</strong><br>
                        <span style="font-size: 8pt; color: #666;">Sejak: {{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d/m/Y') : '-' }}</span>
                    @elseif($asset->status == 'available')
                        Gudang Utama
                    @else
                        Service Center
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $showImages ? 6 : 5 }}" style="text-align: center; padding: 20px; font-style: italic; color: #777;">
                    Tidak ada data aset yang sesuai dengan kriteria filter.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        @if($adminNotes)
        <div class="notes">
            <strong>Catatan Admin:</strong><br>
            {{ $adminNotes }}
        </div>
        @endif

        <div class="signature">
            <p>Mengetahui,</p>
            <br><br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">( {{ auth()->user()->name }} )</p>
            <p>Administrator</p>
        </div>
    </div>
</body>
</html>