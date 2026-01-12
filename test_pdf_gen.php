<?php
// Quick test script untuk verify PDF generation
$pdf = \PDF::loadView('pdf.assets_report', [
    'assets' => [],
    'title' => 'Test PDF',
    'filterStatus' => 'all',
    'filterSort' => 'latest',
    'filterSearch' => '',
    'adminNotes' => 'Test',
    'showImages' => true
]);

echo "PDF Generation: Success\n";
echo "PDF Output path: " . storage_path('app/test-pdf.pdf') . "\n";

try {
    $pdf->save(storage_path('app/test-pdf.pdf'));
    echo "PDF Saved: OK\n";
} catch (\Exception $e) {
    echo "PDF Save Error: " . $e->getMessage() . "\n";
}
