<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $customTitle ?? 'Laporan Aset' }}</title>
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
            min-width: 50px;
        }
        .st-available { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .st-deployed { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
        .st-maintenance { background: #fef9c3; color: #854d0e; border-color: #fde047; }
        .st-broken { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

        /* FOOTER & PAGE NUMBER */
        .footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            border-top: 1px solid #000; padding-top: 5px;
            font-size: 8px; text-align: right; color: #555;
        }
        .page-number:after { content: counter(page); }
        
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
    </style>
</head>
<body>

    {{-- PHP HELPER: Convert Image to Base64 --}}
    @php
        function imgToBase64($path) {
            if (!file_exists($path)) return null;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        $logoPath = public_path('img/logoVitechAsia.png');
        $logoBase64 = imgToBase64($logoPath);
    @endphp

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
                <h1>{{ $customTitle ?? 'Laporan Inventaris Aset' }}</h1>
                <h2>PT VITECH ASIA - INTEGRATED ASSET MANAGEMENT SYSTEM</h2>
                <p>Dicetak: {{ date('d F Y, H:i') }} WIB | Oleh: {{ auth()->user()->name }}</p>
            </td>
        </tr>
    </table>

    {{-- META INFO --}}
    <div class="meta-info">
        <table class="meta-table">
            <tr>
                <td width="15%"><strong>Kategori Filter:</strong></td>
                <td width="35%">{{ $filterCategory ?? 'Semua' }}</td>
                <td width="15%"><strong>Status Filter:</strong></td>
                <td width="35%">{{ $filterStatus ?? 'Semua' }}</td>
            </tr>
            <tr>
                <td><strong>Pencarian:</strong></td>
                <td>{{ $filterSearch ?? '-' }}</td>
                <td><strong>Total Data:</strong></td>
                <td>{{ count($assets) }} Unit</td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <table class="data">
        <thead>
            <tr>
                <th width="20">No</th>
                @if(isset($showImages) && $showImages) <th width="50">Foto</th> @endif
                <th>Detail Aset (Nama & SN)</th>
                <th width="80">Kategori</th>
                <th>Lokasi</th>
                <th width="70">Status & Stok</th>
                <th width="50">QR Code</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                
                {{-- FOTO ASET --}}
                @if(isset($showImages) && $showImages)
                <td class="text-center">
                    @php
                        $assetImgBase64 = null;
                        if($asset->image) {
                            $fullPath = storage_path('app/public/' . $asset->image);
                            if(!file_exists($fullPath)) {
                                $fullPath = public_path('storage/' . $asset->image);
                            }
                            $assetImgBase64 = imgToBase64($fullPath);
                        }
                    @endphp

                    @if($assetImgBase64)
                        <img src="{{ $assetImgBase64 }}" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #ccc;">
                    @else
                        <span style="color: #ccc; font-size: 8px;">-</span>
                    @endif
                </td>
                @endif

                {{-- NAMA & SN --}}
                <td>
                    <strong style="font-size: 11px;">{{ $asset->name }}</strong><br>
                    <span class="font-mono" style="font-size: 9px; color: #555;">{{ $asset->serial_number }}</span>
                    @if($asset->status == 'deployed' && $asset->holder)
                        <div style="margin-top: 3px; font-size: 8px; color: #1e40af;">
                            <strong>User:</strong> {{ $asset->holder->name }}
                        </div>
                    @endif
                </td>
                
                {{-- KATEGORI --}}
                <td class="text-center">
                    {{ $asset->category ?? '-' }}
                </td>

                {{-- LOKASI --}}
                <td class="text-center">
                    @if($asset->status == 'deployed')
                        <span style="font-style: italic; color: #666;">Sedang Dipinjam</span>
                    @else
                        {{ $asset->lorong ?? '-' }} / {{ $asset->rak ?? '-' }}
                    @endif
                </td>

                {{-- STATUS & STOK --}}
                <td class="text-center">
                    @php
                        $stClass = match($asset->status) {
                            'available' => 'st-available',
                            'deployed' => 'st-deployed',
                            'maintenance' => 'st-maintenance',
                            'broken' => 'st-broken',
                            default => ''
                        };
                    @endphp
                    <span class="badge {{ $stClass }}">{{ ucfirst($asset->status) }}</span>
                    <div style="margin-top: 4px; font-size: 9px;">
                        Stok: <b>{{ $asset->quantity }}</b>
                    </div>
                    @if($asset->condition_notes)
                    <div style="margin-top: 2px; font-size: 7px; font-style: italic; color: #666;">
                        Cond: {{ Str::limit($asset->condition_notes, 10) }}
                    </div>
                    @endif
                </td>

                {{-- QR CODE (VERSI SVG: ANTI ERROR IMAGICK) --}}
                <td class="text-center">
                    @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                        {{-- Gunakan format SVG yang ringan dan tidak butuh Imagick --}}
                        <img src="data:image/svg+xml;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->margin(0)->generate(route('assets.scan', $asset->id))) !!}" style="width: 45px;">
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ (isset($showImages) && $showImages) ? 7 : 6 }}" style="text-align: center; padding: 20px;">
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