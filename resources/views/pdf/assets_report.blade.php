<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; color: #222; }
        .header p { margin: 5px 0; font-size: 9pt; color: #666; }
        
        .meta-info { margin-bottom: 15px; font-size: 9pt; background: #f8f9fa; padding: 10px; border: 1px solid #eee; }
        .meta-item { margin-bottom: 4px; }
        .badge { padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8pt; color: #fff; display: inline-block; }
        .bg-green { background-color: #10b981; } .bg-blue { background-color: #3b82f6; } .bg-red { background-color: #ef4444; } .bg-gray { background-color: #6b7280; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        th { bg-color: #2d3748; color: white; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        .img-cell { width: 60px; text-align: center; }
        .img-cell img { width: 50px; height: 50px; object-fit: cover; border: 1px solid #ccc; padding: 2px; background: #fff; }
        
        .footer { margin-top: 30px; font-size: 9pt; page-break-inside: avoid; }
        .notes { border: 1px dashed #aaa; padding: 10px; margin-bottom: 20px; background: #fffbeb; }
        .signature { text-align: right; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Dicetak Otomatis oleh Sistem Manajemen Aset (SIMASET)</p>
    </div>

    <div class="meta-info">
        <div class="meta-item"><strong>Tanggal Cetak:</strong> {{ $printTime }}</div>
        <div class="meta-item"><strong>Filter Status:</strong> {{ ucfirst($filterStatus) }}</div>
        
        {{-- Tampilkan Info Search jika ada --}}
        @if($filterSearch)
            <div class="meta-item" style="color: #d97706;">
                <strong>Filter Pencarian:</strong> "<i>{{ $filterSearch }}</i>"
            </div>
        @endif
        
        <div class="meta-item"><strong>Total Data:</strong> {{ count($assets) }} Aset</div>
    </div>

    <table>
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
                        <span style="color: #ccc; font-size: 8pt;">No IMG</span>
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
            <strong>Catatan Tambahan:</strong><br>
            {{ $adminNotes }}
        </div>
        @endif

        <div class="signature">
            <p>Mengetahui,</p>
            <br><br><br>
            <p style="text-decoration: underline; font-weight: bold;">( {{ auth()->user()->name }} )</p>
            <p>Administrator</p>
        </div>
    </div>
</body>
</html>