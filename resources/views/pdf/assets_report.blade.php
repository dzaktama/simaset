<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        
        /* Header dengan Tabel agar Logo Rapi */
        .header-table { width: 100%; border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; }
        .header-table td { border: none; vertical-align: middle; }
        .logo-cell { width: 15%; text-align: left; }
        .title-cell { width: 85%; text-align: center; }
        
        .header h1 { margin: 0; font-size: 20pt; color: #333; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #666; font-size: 10pt; }
        
        /* Tabel Data Utama */
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        table.data th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 9pt; }
        
        /* Utilitas */
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 8pt; font-weight: bold; text-transform: uppercase; }
        .img-cell { width: 60px; text-align: center; }
        .img-cell img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; text-align: right; font-size: 10pt; padding-right: 50px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('img/logoVitechAsia.png') }}" style="width: 200px; height: auto;">
            </td>
            <td class="title-cell header">
                <h1>{{ $title }}</h1>
                <p>Dicetak pada: {{ $printTime }}</p>
                <p><strong>Filter Laporan:</strong> {{ $filterStatus }}</p>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 10%;">Foto</th>
                <th style="width: 25%;">Nama Aset & SN</th>
                <th style="width: 8%; text-align: center;">Stok</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 20%;">Kondisi</th>
                <th style="width: 20%;">Lokasi / Peminjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                
                <td class="img-cell">
                    @if($asset->image)
                        <img src="{{ public_path('storage/' . $asset->image) }}" alt="img">
                    @else
                        <span style="color: #ccc;">-</span>
                    @endif
                </td>
                
                <td>
                    <div style="font-weight: bold; font-size: 11pt;">{{ $asset->name }}</div>
                    <div style="color: #555; font-family: monospace; font-size: 9pt; margin-top: 4px;">
                        SN: {{ $asset->serial_number }}
                    </div>
                </td>
                
                <td class="text-center"><strong>{{ $asset->quantity }}</strong> Unit</td>
                
                <td>
                    @php
                        $color = match($asset->status) {
                            'available' => '#d1fae5', // Hijau muda
                            'deployed' => '#dbeafe',  // Biru muda
                            'maintenance' => '#fef3c7', // Kuning
                            'broken' => '#fee2e2',    // Merah muda
                            default => '#f3f4f6'
                        };
                        $textStatus = match($asset->status) {
                            'available' => 'Tersedia',
                            'deployed' => 'Dipakai',
                            'maintenance' => 'Perbaikan',
                            'broken' => 'Rusak',
                            default => ucfirst($asset->status)
                        };
                    @endphp
                    <span class="status-badge" style="background-color: {{ $color }};">
                        {{ $textStatus }}
                    </span>
                </td>
                
                <td>{{ $asset->condition_notes ?? '-' }}</td>
                
                <td>
                    @if($asset->status == 'deployed' && $asset->holder)
                        <strong>{{ $asset->holder->name }}</strong>
                        <br><small style="color: #666;">Sejak: {{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d/m/y H:i') : '-' }}</small>
                    @elseif($asset->status == 'available')
                        Gudang IT
                    @elseif($asset->status == 'maintenance')
                        Service Center
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>( IT Administrator )</p>
    </div>

</body>
</html>