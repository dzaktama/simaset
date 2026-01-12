<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Aset' }}</title>
    <style>
        /* SETUP HALAMAN CETAK */
        @page {
            size: A4 {{ $orientation ?? 'portrait' }};
            margin: 10mm 15mm 15mm 15mm; 
        }

        /* RESET & BASE STYLES */
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* SAAT DICETAK / DI PREVIEW IFRAME */
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            
            .page-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 30px;
                border-top: 1px solid #000;
                padding-top: 5px;
                background: #fff;
            }

            thead { display: table-header-group; }
            tfoot { display: table-row-group; }
            tr { page-break-inside: avoid; }
        }

        /* HEADER LAPORAN */
        .header-container {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        /* [REVISI 2] Logo Diperbesar dari 80px ke 120px */
        .logo-wrapper { 
            width: 120px; 
            flex-shrink: 0; 
        }
        .logo-img { 
            width: 100%; 
            height: auto; 
        }
        /* Padding kanan header teks disamakan dgn lebar logo agar teks tetap di tengah page */
        .header-content { 
            flex-grow: 1; 
            text-align: center; 
            padding-right: 120px; 
        }
        
        .header-content h1 { margin: 0; font-size: 18px; font-weight: 800; text-transform: uppercase; }
        .header-content h2 { margin: 4px 0; font-size: 12px; font-weight: bold; color: #333; }
        .header-content p { margin: 0; font-size: 10px; color: #555; }

        /* INFO BOX */
        .meta-box {
            border: 1px solid #ccc;
            padding: 8px 12px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            background-color: #f9fafb;
        }
        .meta-item { display: flex; flex-direction: column; }
        .meta-label { font-weight: bold; color: #555; text-transform: uppercase; font-size: 8px; }
        .meta-value { font-weight: bold; margin-top: 2px; }

        /* TABEL DATA */
        table.data-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 40px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; text-align: left; }
        table.data-table th { background-color: #eee; font-weight: bold; text-transform: uppercase; font-size: 9px; text-align: center; }
        table.data-table tr:nth-child(even) { background-color: #fcfcfc; }

        /* COMPONENTS */
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; text-transform: uppercase; display: inline-block; min-width: 60px; text-align: center; }
        .bg-available { background-color: #10b981; }
        .bg-deployed { background-color: #3b82f6; }
        .bg-maintenance { background-color: #f59e0b; }
        .bg-broken { background-color: #ef4444; }
        .asset-img { width: 40px; height: 40px; object-fit: cover; border: 1px solid #ccc; }

        /* PAGE NUMBERING */
        @page {
            @bottom-right {
                content: "Halaman " counter(page);
                font-size: 9px;
            }
        }
        body { counter-reset: page 0; }
        .page-number::after { 
            counter-increment: page;
            content: "Hal: " counter(page); 
        }
    </style>
</head>
<body>

    <div class="page-footer no-print" style="display: flex; justify-content: space-between; font-size: 9px; color: #555;">
        <div>Dicetak oleh: <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></div>
        <div>{{ config('app.name') }} &copy; {{ date('Y') }}</div>
    </div>

    <div class="header-container">
        <div class="logo-wrapper">
            <img src="{{ asset('img/logoVitechAsia.png') }}" class="logo-img" alt="Logo">
        </div>
        <div class="header-content">
            <h1>{{ $customTitle ?? 'Laporan Aset IT' }}</h1>
            <h2>PT VITECH ASIA - INTEGRATED ASSET MANAGEMENT</h2>
            <p>Tanggal: {{ $date }} | Pukul: {{ $printTime }} WIB</p>
        </div>
    </div>

    <div class="meta-box">
        <div style="display:flex; gap: 30px;">
            <div class="meta-item">
                <span class="meta-label">Total Aset</span>
                <span class="meta-value">{{ count($assets) }} Unit</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Filter Status</span>
                <span class="meta-value">{{ $filterStatus }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Pencarian</span>
                <span class="meta-value">{{ $filterSearch ?: '-' }}</span>
            </div>
        </div>
        <div class="meta-item" style="text-align: right;">
            <span class="meta-label">Catatan Admin</span>
            <span class="meta-value" style="font-style: italic;">{{ Str::limit($adminNotes, 100) }}</span>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                @if($showImages) <th style="width: 8%;">Foto</th> @endif
                <th style="width: 25%;">Nama Aset / Spesifikasi</th>
                <th style="width: 15%;">Serial Number</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 8%;">Stok</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 15%;">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                
                @if($showImages)
                <td style="text-align: center;">
                    @if($asset->image)
                        <img src="{{ asset('storage/' . $asset->image) }}" class="asset-img">
                    @else - @endif
                </td>
                @endif

                <td>
                    <div style="font-weight: bold;">{{ $asset->name }}</div>
                    <div style="font-size: 8px; color: #555;">{{ Str::limit($asset->description, 50) }}</div>
                </td>
                <td style="font-family: monospace;">{{ $asset->serial_number }}</td>
                <td>{{ $asset->category ?? '-' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $asset->quantity }}</td>
                <td style="text-align: center;">
                    @php
                        $color = match($asset->status) {
                            'available' => 'bg-available',
                            'deployed' => 'bg-deployed',
                            'maintenance' => 'bg-maintenance',
                            'broken' => 'bg-broken',
                            default => 'bg-gray'
                        };
                    @endphp
                    <span class="badge {{ $color }}">{{ ucfirst($asset->status) }}</span>
                    @if($asset->condition_notes)
                        <div style="font-size: 7px; margin-top: 2px;">{{ Str::limit($asset->condition_notes, 15) }}</div>
                    @endif
                </td>
                <td>
                    @if($asset->status == 'deployed' && $asset->holder)
                        <strong>{{ $asset->holder->name }}</strong>
                    @else
                        {{ $asset->location ?? 'Gudang' }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>