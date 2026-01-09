@extends('layouts.main')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Generator Laporan Aset</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan</li>
    </ol>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-sliders-h me-1"></i> Kustomisasi Laporan
                </div>
                <div class="card-body">
                    <form id="reportForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Kata Kunci (Search)</label>
                            <input type="text" id="inputSearch" class="form-control" placeholder="Contoh: Cisco, Laptop, atau SN...">
                            <div class="form-text text-muted small">Cari berdasarkan Nama Aset atau Serial Number.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status Aset</label>
                            <select id="inputStatus" class="form-select">
                                <option value="all">Semua Status</option>
                                <option value="available">Available (Tersedia)</option>
                                <option value="deployed">Deployed (Dipakai)</option>
                                <option value="maintenance">Maintenance (Perbaikan)</option>
                                <option value="broken">Broken (Rusak)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Urutkan Data</label>
                            <select id="inputSort" class="form-select">
                                <option value="newest">Terbaru Ditambahkan</option>
                                <option value="oldest">Terlama</option>
                                <option value="stock_high">Stok Terbanyak</option>
                                <option value="stock_low">Stok Sedikit</option>
                            </select>
                        </div>

                        <hr>

                        <div class="alert alert-light border small">
                            <strong>Info Data Saat Ini:</strong><br>
                            Total Aset: {{ $totalAssets ?? '-' }} Unit<br>
                            Tersedia: <span class="text-success">{{ $availableAssets ?? '-' }}</span> | 
                            Dipakai: <span class="text-primary">{{ $deployedAssets ?? '-' }}</span>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-dark" onclick="refreshPreview()">
                                <i class="fas fa-sync-alt me-1"></i> Terapkan & Preview
                            </button>
                            <button type="button" class="btn btn-danger" onclick="downloadPDF()">
                                <i class="fas fa-file-pdf me-1"></i> Download / Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-eye me-1"></i> Live Preview</span>
                    <span class="badge bg-secondary" id="previewStatus">Menunggu Filter...</span>
                </div>
                <div class="card-body p-0" style="height: 600px; background-color: #525659;">
                    <iframe id="pdfPreviewFrame" src="" width="100%" height="100%" style="border: none;">
                        <p class="text-white text-center p-5">Browser Anda tidak mendukung preview PDF.</p>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi membangun URL berdasarkan input form
    function getReportUrl() {
        const search = document.getElementById('inputSearch').value;
        const status = document.getElementById('inputStatus').value;
        const sort = document.getElementById('inputSort').value;

        // Panggil route 'report.print' dengan parameter query string
        return `{{ route('report.print') }}?search=${encodeURIComponent(search)}&status=${status}&sort=${sort}`;
    }

    // Fungsi Refresh Preview di Iframe
    function refreshPreview() {
        const url = getReportUrl();
        const iframe = document.getElementById('pdfPreviewFrame');
        const statusBadge = document.getElementById('previewStatus');
        
        statusBadge.innerText = "Memuat...";
        statusBadge.className = "badge bg-warning text-dark";

        iframe.src = url;

        iframe.onload = function() {
            statusBadge.innerText = "Siap Dicetak";
            statusBadge.className = "badge bg-success";
        };
    }

    // Fungsi Tombol Download (Buka di Tab Baru)
    function downloadPDF() {
        const url = getReportUrl();
        window.open(url, '_blank');
    }

    // Load preview pertama kali saat halaman dibuka
    document.addEventListener("DOMContentLoaded", function() {
        refreshPreview();
    });
</script>
@endsection