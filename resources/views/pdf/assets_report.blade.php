<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .meta { margin-bottom: 15px; font-size: 9pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        th { bg-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .status-badge { padding: 3px 6px; border-radius: 4px; font-size: 8pt; font-weight: bold; }
        .img-cell { width: 60px; text-align: center; }
        .img-cell img { width: 50px; height: 50px; object-fit: cover; border-radius: 3px; border: 1px solid #eee; }
        .notes-box { margin-top: 30px; padding: 15px; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 5px; }
        .notes-title { font-weight: bold; font-size: 9pt; margin-bottom: 5px; color: #333; }
        .notes-content { font-style: italic; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Dicetak pada: {{ $printTime }} | Filter: {{ ucfirst($filterStatus) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                @if($showImages) <th style="width: 10%;">Foto</th> @endif
                <th>Nama Aset</th>
                <th>Serial Number</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Lokasi / Peminjam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                
                @if($showImages)
                <td class="img-cell">
                    @if($asset->image)
                        {{-- Gunakan public_path untuk PDF agar gambar load --}}
                        <img src="{{ public_path('storage/' . $asset->image) }}" alt="img">
                    @else
                        -
                    @endif
                </td>
                @endif
                
                <td>
                    <strong>{{ $asset->name }}</strong>
                    <br><small style="color: #777;">Added: {{ $asset->created_at->format('d/m/Y') }}</small>
                </td>
                <td style="font-family: monospace;">{{ $asset->serial_number }}</td>
                <td style="text-align: center;">{{ $asset->quantity }}</td>
                <td>
                    <span class="status-badge" style="background-color: {{ $asset->status == 'available' ? '#d1fae5' : ($asset->status == 'deployed' ? '#dbeafe' : '#fee2e2') }}">
                        {{ ucfirst($asset->status) }}
                    </span>
                </td>
                <td>{{ $asset->condition_notes ?? '-' }}</td>
                <td>
                    @if($asset->status == 'deployed' && $asset->holder)
                        <strong>{{ $asset->holder->name }}</strong>
                        <br><small>Sejak: {{ $asset->assigned_date ? \Carbon\Carbon::parse($asset->assigned_date)->format('d M Y') : '-' }}</small>
                    @elseif($asset->status == 'available')
                        Gudang IT
                    @else
                        Service Center
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Catatan Admin (Jika Ada) --}}
    @if($adminNotes)
    <div class="notes-box">
        <div class="notes-title">Catatan Tambahan:</div>
        <div class="notes-content">{{ $adminNotes }}</div>
    </div>
    @endif

    <div style="margin-top: 30px; text-align: right; font-size: 9pt;">
        <p>Mengetahui,<br><br><br><br>( Administrator )</p>
    </div>
</body>
</html>