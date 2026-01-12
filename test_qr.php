<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

$asset = \App\Models\Asset::first();
if ($asset) {
    echo "QR Code (first 150 chars): \n";
    echo substr($asset->qr_code, 0, 150) . "...\n";
    echo "\nFull length: " . strlen($asset->qr_code) . " chars\n";
    echo "\nStarts with: " . (strpos($asset->qr_code, 'data:') === 0 ? 'data: URL ✓' : 'External URL ✗');
} else {
    echo "No assets found\n";
}
