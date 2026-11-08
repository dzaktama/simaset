<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi Denda #{{ $return->id }} - SIMASET</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        *{box-sizing:border-box} body{font-family:'Inter',sans-serif;background:#f3f4f6;margin:0;padding:2rem 1rem;color:#1f2937}
        .sheet{max-width:640px;margin:0 auto;background:#fff;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,.08);overflow:hidden}
        .top{background:#4f46e5;color:#fff;padding:1.5rem 2rem;display:flex;justify-content:space-between;align-items:center}
        .top h1{margin:0;font-size:1.25rem}.top small{opacity:.8}
        .body{padding:2rem}.row{display:flex;justify-content:space-between;gap:1rem;padding:.65rem 0;border-bottom:1px dashed #e5e7eb;font-size:.9rem}
        .row span:first-child{color:#6b7280}.row span:last-child{font-weight:600;text-align:right}
        .total{margin:1.5rem 0;padding:1rem 1.25rem;border-radius:12px;background:#eef2ff;display:flex;justify-content:space-between;align-items:center}
        .total strong{font-size:1.5rem;color:#4338ca}
        .stamp{display:inline-block;padding:.25rem .8rem;border-radius:999px;font-size:.75rem;font-weight:700}
        .paid{background:#ecfdf5;color:#047857}.waived{background:#f1f5f9;color:#475569}
        .sign{display:flex;justify-content:space-between;margin-top:2.5rem;font-size:.85rem;text-align:center}
        .sign div{width:45%}.sign .line{margin-top:3.5rem;border-top:1px solid #9ca3af;padding-top:.3rem;font-weight:600}
        .actions{max-width:640px;margin:1rem auto 0;display:flex;gap:.5rem;justify-content:flex-end}
        .btn{padding:.55rem 1rem;border-radius:.6rem;border:1px solid #d1d5db;background:#fff;font-weight:700;font-size:.8rem;cursor:pointer;text-decoration:none;color:#374151}
        .btn.primary{background:#4f46e5;color:#fff;border-color:#4f46e5}
        @media print{body{background:#fff;padding:0}.sheet{box-shadow:none}.actions{display:none}}
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ url()->previous() }}" class="btn">Kembali</a>
        <button class="btn primary" onclick="window.print()">Cetak</button>
    </div>
    <div class="sheet" style="margin-top:1rem">
        <div class="top">
            <div><h1>Kwitansi Penyelesaian Denda</h1><small>SIMASET &middot; PT Vitech Asia</small></div>
            <div style="text-align:right"><small>No.</small><br><strong>DND-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
        </div>
        <div class="body">
            <div class="row"><span>Peminjam</span><span>{{ $return->user->name ?? '-' }}</span></div>
            <div class="row"><span>Aset</span><span>{{ $return->asset->name ?? '-' }} ({{ $return->asset->serial_number ?? '-' }})</span></div>
            <div class="row"><span>Tanggal pengembalian</span><span>{{ $return->return_date?->translatedFormat('d F Y, H:i') }}</span></div>
            <div class="row"><span>Kondisi akhir</span><span>{{ ['good' => 'Baik', 'available' => 'Baik', 'maintenance' => 'Perlu Servis', 'broken' => 'Rusak'][$return->condition] ?? $return->condition }}</span></div>
            <div class="row"><span>Diverifikasi oleh</span><span>{{ $return->admin->name ?? '-' }}</span></div>
            <div class="row"><span>Status</span><span><i class="stamp {{ $return->fine_status === 'paid' ? 'paid' : 'waived' }}" style="font-style:normal">{{ $return->fine_status === 'paid' ? 'LUNAS' : 'DIBEBASKAN' }}</i></span></div>
            @if($return->fine_status === 'paid')
                <div class="row"><span>Metode pembayaran</span><span>{{ $return->fine_payment_method === 'transfer' ? 'Transfer' : 'Tunai' }}</span></div>
            @endif
            <div class="row"><span>{{ $return->fine_status === 'paid' ? 'Tanggal pembayaran' : 'Tanggal dibebaskan' }}</span><span>{{ $return->fine_paid_at?->translatedFormat('d F Y, H:i') }}</span></div>
            <div class="row"><span>Dikonfirmasi oleh</span><span>{{ $return->fineConfirmer->name ?? '-' }}</span></div>
            @if($return->fine_note)<div class="row"><span>Catatan</span><span>{{ $return->fine_note }}</span></div>@endif

            <div class="total">
                <span>{{ $return->fine_status === 'paid' ? 'Total dibayar' : 'Denda yang dibebaskan' }}</span>
                <strong>Rp {{ number_format($return->fine, 0, ',', '.') }}</strong>
            </div>

            <div class="sign">
                <div>Peminjam<div class="line">{{ $return->user->name ?? '' }}</div></div>
                <div>Admin / Penerima<div class="line">{{ $return->fineConfirmer->name ?? '' }}</div></div>
            </div>
        </div>
    </div>
</body>
</html>
