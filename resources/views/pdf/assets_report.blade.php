<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #333; }
        .header p { margin: 2px 0; font-size: 9pt; color: #666; }
        
        .meta { margin-bottom: 15px; font-size: 9pt; color: #555; }
        .meta table { width: 100%; }
        .meta td { padding: 2px; }
        .text-right { text-align: right; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        table.data th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        table.data td { font-size: 9pt; }
        
        .status-badge { font-weight: bold; font-size: 8pt; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN INVENTARIS ASET IT</h1>
        <p>PT. Vitech Asia - System Management Asset</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td><strong>Filter Status:</strong> {{ ucfirst($filterStatus) }}</td>
                <td class="text-right"><strong>Waktu Cetak:</strong> {{ $printTime }}</td>
            </tr>
            <tr>
                <td><strong>Total Data:</strong> {{ $assets->count() }} unit</td>
                <td class="text-right"><strong>Oleh:</strong> {{ auth()->user()->name }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Barang & SN</th>
                <th style="width: 15%">Kondisi</th>
                <th style="width: 10%">Status</th>
                <th style="width: 20%">Pemegang (Holder)</th>
                <th style="width: 25%">Detail Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $asset->name }}</strong><br>
                    <span style="color: #666; font-size: 8pt;">SN: {{ $asset->serial_number }}</span>
                </td>
                <td>{{ $asset->condition_notes ?? '-' }}</td>
                <td>
                    <span class="status-badge">
                        {{ strtoupper($asset->status) }}
                    </span>
                </td>
                <td>
                    @if($asset->holder)
                        {{ $asset->holder->name }}
                    @else
                        <span style="color: #999; font-style: italic;">- Gudang -</span>
                    @endif
                </td>
                <td>
                    @if($asset->status == 'deployed' && $asset->latestApprovedRequest)
                        <div style="font-size: 8pt;">
                            Pinjam: {{ \Carbon\Carbon::parse($asset->latestApprovedRequest->request_date)->format('d/m/Y H:i') }}<br>
                            @if($asset->latestApprovedRequest->return_date)
                                Kembali: {{ \Carbon\Carbon::parse($asset->latestApprovedRequest->return_date)->format('d/m/Y') }}
                            @else
                                <span style="color: red;">(Tidak ada tgl kembali)</span>
                            @endif
                        </div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh sistem SIMASET pada {{ $printTime }}. Halaman ini dokumen rahasia perusahaan.
    </div>

</body>
</html>