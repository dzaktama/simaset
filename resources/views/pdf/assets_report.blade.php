<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aset Vitech Asia</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header td { vertical-align: middle; }
        .logo { width: 80px; height: auto; }
        .company-name { font-size: 18px; font-weight: bold; }
        .company-address { font-size: 10px; color: #555; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #333; padding: 6px; text-align: left; }
        table.data th { bg-color: #f0f0f0; }
        
        .footer { margin-top: 40px; text-align: right; }
        .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td width="15%">
                {{-- Pastikan file gambar ada di public/img/logoVitechAsia.png --}}
                <img src="{{ public_path('img/logoVitechAsia.png') }}" class="logo" alt="Logo">
            </td>
            <td width="85%" align="center">
                <div class="company-name">PT. VITECH ASIA</div>
                <div class="company-address">
                    Gedung Vitech Tower Lt. 5, Jl. Teknologi No. 12, Jakarta Selatan<br>
                    Telp: (021) 555-7788 | Email: it-support@vitechasia.com
                </div>
            </td>
        </tr>
    </table>

    <h3 style="text-align: center;">LAPORAN INVENTARIS ASET IT</h3>
    <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y') }}</p>

    <table class="data">
        <thead>
            <tr style="background-color: #eee;">
                <th>No</th>
                <th>Nama Barang</th>
                <th>Serial Number</th>
                <th>Status</th>
                <th>Peminjam (Holder)</th>
                <th>Tgl Beli</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->serial_number }}</td>
                <td>{{ ucfirst($asset->status) }}</td>
                <td>
                    @if($asset->user_id)
                        {{ $asset->holder->name }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $asset->purchase_date ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Jakarta, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Mengetahui,<br>IT Manager</p>
        <div class="signature">Budi Santoso, S.Kom</div>
    </div>

</body>
</html>