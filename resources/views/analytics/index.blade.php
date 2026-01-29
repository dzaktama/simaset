@extends('layouts.main')

@section('container')
{{-- Compact Full Width Container --}}
<div class="w-full px-3 md:px-5 pb-8 space-y-5">
    
    {{-- Scaled Down Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-gray-200 pb-3">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pusat Analisis Data</h2>
            <p class="mt-1 text-sm text-gray-600">
                Pusat kendali operasional dengan filter data mendalam per metrik.
            </p>
        </div>
        <div class="text-right">
            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Hari Ini</span>
            <span class="text-base font-bold text-gray-800">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- CHART LIST - COMPACT VERTICAL STACK --}}
    <div class="space-y-6" id="charts-container">
        
        @include('analytics.partials.chart-row', [
            'id' => 'borrowingTrend',
            'title' => 'Tren Peminjaman Aset',
            'desc' => 'Fluktuasi permintaan aset (Disetujui vs Ditolak).',
            'insight' => 'Analisis Tren Peminjaman: Lonjakan signifikan pada periode tertentu mengindikasikan tingginya kebutuhan operasional. Jika tren "Ditolak" meningkat, evaluasi kebijakan stok dan ketersediaan aset. Disarankan menambah aset jika tren kenaikan konsisten dalam 3 bulan.',
            'status' => 'Positif',
            'statusColor' => 'text-green-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'purchaseTrend',
            'title' => 'Tren Penambahan Aset',
            'desc' => 'Inventaris aset baru yang didaftarkan.',
            'insight' => 'Strategi Pengadaan: Grafik memvisualisasikan realisasi belanja modal (Capex). Gunakan data ini untuk menyelaraskan rencana anggaran tahunan dan memastikan tidak ada pembelian impulsif yang tidak terencana.',
            'status' => 'Stabil',
            'statusColor' => 'text-blue-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'maintenanceCost',
            'title' => 'Biaya Maintenance',
            'desc' => 'Pengeluaran perbaikan.',
            'insight' => 'Efisiensi Operasional: Waspadai kategori aset dengan biaya perbaikan tinggi ("Money Pit"). Jika biaya maintenance kumulatif melebihi 50% harga beli baru, direkomendasikan untuk melakukan peremajaan (disposal & replacement).',
            'status' => 'Perhatian',
            'statusColor' => 'text-orange-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'returnCompliance',
            'title' => 'Kepatuhan Pengembalian',
            'desc' => 'Tepat waktu vs Terlambat.',
            'insight' => 'Kedisiplinan User: Rasio keterlambatan yang tinggi (>20%) berpotensi mengganggu ketersediaan stok untuk user lain. Perlu dipertimbangkan penegakan kebijakan denda atau pembatasan akses bagi pelanggar berulang.',
            'status' => 'Normal',
            'statusColor' => 'text-indigo-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'assetReliability',
            'title' => 'Frekuensi Kerusakan',
            'desc' => 'Tiket per kategori.',
            'insight' => 'Evaluasi Kualitas: Jika merek atau kategori tertentu mendominasi tiket kerusakan, segera hentikan pengadaan merek tersebut. Fokuskan pembelian pada aset dengan durabilitas yang terbukti lebih baik.',
            'status' => 'Info',
            'statusColor' => 'text-gray-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'topUsers',
            'title' => 'Top 5 Peminjam',
            'desc' => 'User frekuensi tinggi.',
            'insight' => 'Apresiasi & Kontrol: User dalam daftar ini adalah pengguna paling aktif. Pertimbangkan pemberian akses prioritas atau reward jika kepatuhannya baik. Sebaliknya, lakukan audit mendalam jika frekuensi peminjaman mereka tidak wajar.',
            'status' => 'Aktif',
            'statusColor' => 'text-purple-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'topAssets',
            'title' => 'Aset Populer',
            'desc' => 'Sering dipinjam.',
            'insight' => 'Manajemen Stok: Aset-aset ini memiliki turn-over tinggi. Pastikan stok selalu mencukupi dengan safety stock minimal 20% untuk mencegah bottleneck operasional akibat kelangkaan barang.',
            'status' => 'Populer',
            'statusColor' => 'text-pink-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />'
        ])

        @include('analytics.partials.chart-row', [
            'id' => 'assetAging',
            'title' => 'Umur Aset',
            'desc' => 'Usia sejak beli.',
            'insight' => 'Lifecycle Management: Aset yang berusia > 3 tahun (Grafik batang kanan) memiliki risiko kerusakan tinggi. Siapkan anggaran depresiasi dan prioritas disposal untuk menjaga efisiensi kinerja perusahaan.',
            'status' => 'Monitor',
            'statusColor' => 'text-yellow-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
        ])
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
                
                <div class="bg-indigo-600 px-5 py-3 flex justify-between items-center bg-gradient-to-r from-indigo-600 to-blue-600">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">DETAIL</h3>
                    <button onclick="closeDetailModal()" class="text-white hover:bg-white/20 p-1 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="bg-white p-5 md:p-6 max-h-[75vh] overflow-y-auto">
                    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50"><tr id="modal-thead-tr"></tr></thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-xs sm:text-sm" id="modal-tbody">
                                <tr><td class="p-6 text-center text-gray-400">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-50 px-5 py-3 flex justify-end">
                    <button type="button" class="px-5 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm text-gray-700 font-bold hover:bg-gray-100" onclick="closeDetailModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
<script>
    const chartInstances = {};
    const chartConfig = {
        borrowingTrend: { type: 'line', color: 'rgb(16, 185, 129)', fill: true, smooth: true },
        purchaseTrend: { type: 'line', color: 'rgb(16, 185, 129)', fill: true, smooth: true }, // Changed to Green to match Dashboard
        maintenanceCost: { type: 'line', color: 'rgb(79, 70, 229)', fill: true, smooth: true },
        returnCompliance: { type: 'doughnut' },
        assetReliability: { type: 'bar', color: 'rgb(239, 68, 68)' },
        topUsers: { type: 'bar', indexAxis: 'y', color: 'rgb(139, 92, 246)' },
        topAssets: { type: 'bar', indexAxis: 'y', color: 'rgb(245, 158, 11)' },
        assetAging: { type: 'bar', color: 'rgb(107, 114, 128)' },
    };

    const chartState = {};

    function initChartState(id) {
        if(!chartState[id]) {
            chartState[id] = { mode: 'month', startDate: '', endDate: '' };
            updateButtonState(id, 'month');
            const startInput = document.getElementById(`start-${id}`);
            const endInput = document.getElementById(`end-${id}`);
            if(startInput) startInput.addEventListener('change', (e) => { chartState[id].startDate = e.target.value; reloadChart(id); });
            if(endInput) endInput.addEventListener('change', (e) => { chartState[id].endDate = e.target.value; reloadChart(id); });
        }
    }

    // Reset Zoom Function for TradingView-style charts
    function resetChartZoom(id) {
        const chart = chartInstances[id];
        if (chart) {
            chart.resetZoom();
            const resetBtn = document.getElementById(`reset-zoom-${id}`);
            if (resetBtn) resetBtn.classList.add('hidden');
        }
    }

    // Show/Hide Reset Button based on zoom/pan state
    function updateResetButtonVisibility(id, isZoomedOrPanned) {
        const resetBtn = document.getElementById(`reset-zoom-${id}`);
        if (resetBtn) {
            if (isZoomedOrPanned) {
                resetBtn.classList.remove('hidden');
                resetBtn.classList.add('flex');
            } else {
                resetBtn.classList.add('hidden');
                resetBtn.classList.remove('flex');
            }
        }
    }

    function updateChart(id, mode) {
        chartState[id].mode = mode;
        updateButtonState(id, mode);
        reloadChart(id);
    }
    
    function updateButtonState(id, activeMode) {
        ['day', 'month', 'year'].forEach(m => {
            const btn = document.getElementById(`btn-${m}-${id}`);
            if(btn) {
                // WARNA ACTIVE TERGANTUNG TIPE CHART
                let activeClassText = 'text-indigo-700';
                if(id === 'purchaseTrend') activeClassText = 'text-emerald-700';

                if(m === activeMode) {
                    btn.classList.add('bg-white', activeClassText, 'shadow-sm');
                    btn.classList.remove('text-indigo-100', 'text-emerald-100', 'hover:bg-white/10');
                } else {
                    btn.classList.remove('bg-white', 'text-indigo-700', 'text-emerald-700', 'shadow-sm');
                    // Add conditional class back
                     if(id === 'purchaseTrend') {
                         btn.classList.add('text-emerald-100');
                     } else {
                         btn.classList.add('text-indigo-100');
                     }
                    btn.classList.add('hover:bg-white/10');
                }
            }
        });
    }

    async function reloadChart(key) {
        const config = chartConfig[key];
        const state = chartState[key];
        const canvas = document.getElementById(`chart-${key}`);
        if (!canvas) return;
        
        const container = canvas.parentElement;
        
        // Show loading state
        if (!container.querySelector('.chart-loading-overlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'chart-loading-overlay absolute inset-0 bg-white/80 flex items-center justify-center z-10';
            loadingOverlay.innerHTML = '<svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
            container.style.position = 'relative';
            container.appendChild(loadingOverlay);
        }
        
        const params = new URLSearchParams({ type: key, mode: state.mode, startDate: state.startDate, endDate: state.endDate });

        try {
            const response = await fetch(`{{ route('analytics.data') }}?${params.toString()}`);
            
            // Error Handling: Cek HTTP Status
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const json = await response.json();
            
            // Error Handling: Cek apakah response mengandung error
            if (json.error) {
                throw new Error(json.error);
            }
            
            if (chartInstances[key]) chartInstances[key].destroy();

            // Logic Scroll: Jika data banyak (>12), perlebar canvas container
            const dataLength = json.labels?.length || 0;
            
            if (['line', 'bar'].includes(config.type) && config.indexAxis !== 'y' && dataLength > 12) {
                const newWidth = Math.max(container.clientWidth, dataLength * 35);
                
                let wrapper = container.querySelector('.chart-scroll-wrapper');
                if (!wrapper) {
                    wrapper = document.createElement('div');
                    wrapper.className = 'chart-scroll-wrapper relative h-full';
                    canvas.parentNode.insertBefore(wrapper, canvas);
                    wrapper.appendChild(canvas);
                }
                wrapper.style.minWidth = `${newWidth}px`;
                wrapper.style.overflowX = 'auto';
                wrapper.style.overflowY = 'hidden';
            } else {
                 const wrapper = container.querySelector('.chart-scroll-wrapper');
                 if (wrapper) {
                    wrapper.style.minWidth = '100%';
                    wrapper.style.overflowX = 'hidden';
                 }
            }

            // Zoom & Pan configuration for TradingView-style scrolling
            const isInteractiveChart = ['line', 'bar'].includes(config.type) && !['doughnut', 'pie'].includes(config.type);
            
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (e) => openDetail(key),
                layout: { padding: 5 },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { 
                        display: json.datasets ? true : ['doughnut', 'pie'].includes(config.type),
                        position: json.datasets ? 'bottom' : 'right',
                        labels: { font: { size: 9, weight: 'bold' }, usePointStyle: true, boxWidth: 5 }
                    },
                    tooltip: {
                         mode: 'index', intersect: false, backgroundColor: 'rgba(255, 255, 255, 0.95)',
                         titleColor: '#1f2937', bodyColor: '#4b5563', borderColor: '#e5e7eb', borderWidth: 1, padding: 8,
                         titleFont: { size: 11, weight: 'bold' }, bodyFont: { size: 10 }, displayColors: true, usePointStyle: true,
                    },
                    // Zoom & Pan Plugin Configuration
                    zoom: isInteractiveChart ? {
                        pan: {
                            enabled: true,
                            mode: 'x', // Horizontal pan only (like TradingView)
                            threshold: 5,
                            onPanStart: ({chart}) => {
                                chart.canvas.style.cursor = 'grabbing';
                            },
                            onPanComplete: ({chart}) => {
                                chart.canvas.style.cursor = 'grab';
                                updateResetButtonVisibility(key, true);
                            },
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                                modifierKey: 'ctrl', // Ctrl + Scroll untuk zoom
                            },
                            pinch: {
                                enabled: true // Pinch zoom untuk touch devices
                            },
                            mode: 'x', // Zoom horizontal only
                            onZoomComplete: ({chart}) => {
                                chart.canvas.style.cursor = 'grab';
                                updateResetButtonVisibility(key, true);
                            },
                        },
                        limits: {
                            x: {min: 'original', max: 'original', minRange: 3}, // Min 3 data points visible
                        }
                    } : false
                },
                scales: {
                    x: { 
                        display: !['doughnut', 'pie'].includes(config.type), 
                        grid: { display: false },
                        ticks: { 
                            font: { size: 9 }, color: '#6b7280', 
                            maxRotation: 45, minRotation: 45,
                            autoSkip: true,
                            maxTicksLimit: 15 // Limit visible labels
                        }
                    },
                    y: { 
                         display: !['doughnut', 'pie'].includes(config.type), grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: { font: { size: 9 }, color: '#9ca3af' }, beginAtZero: true
                    }
                }
            };

            // Set grab cursor for interactive charts
            if (isInteractiveChart) {
                canvas.style.cursor = 'grab';
            }
            
            let datasets;

            if (json.datasets) {
                 // MULTI-SERIES SUPPORT (e.g. Borrowing Trend)
                 datasets = json.datasets;
            } else {
                // SINGLE SERIES (Standard)
                const baseColor = json.colors || config.color;
                const bgColor = config.fill ? baseColor.replace('rgb', 'rgba').replace(')', ', 0.1)') : baseColor;
                datasets = [{
                    label: json.label || 'Data', 
                    data: json.data || [], 
                    backgroundColor: bgColor, 
                    borderColor: baseColor,
                    borderWidth: 2, 
                    fill: config.fill || false, 
                    tension: config.smooth ? 0.35 : 0, 
                    borderRadius: 3, 
                    pointRadius: 3, 
                    pointHoverRadius: 5
                }];
            }

            if(['doughnut', 'pie'].includes(config.type)) {
                if(!json.datasets) {
                    datasets[0].backgroundColor = ['#10B981', '#EF4444', '#F59E0B', '#3B82F6', '#8B5CF6'];
                }
                options.scales = {};
            }
            
            chartInstances[key] = new Chart(canvas, { type: config.type, data: { labels: json.labels || [], datasets: datasets }, options: options });
            
        } catch (err) { 
            console.error(`Failed to load chart ${key}:`, err);
            
            // Show error state on chart container
            const existingError = container.querySelector('.chart-error-message');
            if (!existingError) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'chart-error-message absolute inset-0 bg-red-50 flex flex-col items-center justify-center z-10 rounded-lg';
                errorDiv.innerHTML = `
                    <svg class="w-10 h-10 text-red-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-bold text-red-600">Gagal Memuat Grafik</span>
                    <span class="text-xs text-red-400">${err.message || 'Terjadi kesalahan'}</span>
                    <button onclick="reloadChart('${key}')" class="mt-2 px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-bold hover:bg-red-200 transition">Coba Lagi</button>
                `;
                container.style.position = 'relative';
                container.appendChild(errorDiv);
            }
        } finally {
            // Remove loading overlay
            const loadingOverlay = container.querySelector('.chart-loading-overlay');
            if (loadingOverlay) loadingOverlay.remove();
            
            // Remove error if chart loaded successfully
            if (chartInstances[key]) {
                const errorMessage = container.querySelector('.chart-error-message');
                if (errorMessage) errorMessage.remove();
            }
        }
    }

    async function openDetail(type) {
        const modal = document.getElementById('detail-modal');
        const titleEl = document.getElementById('modal-title');
        const thead = document.getElementById('modal-thead-tr');
        const tbody = document.getElementById('modal-tbody');
        
        // Error Handling: Pastikan state sudah terinisialisasi
        if (!chartState[type]) {
            initChartState(type);
        }
        const state = chartState[type];
        
        modal.classList.remove('hidden');
        tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-gray-500 font-medium animate-pulse text-sm"><svg class="animate-spin h-5 w-5 mx-auto mb-2 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mengambil Data...</td></tr>';
        thead.innerHTML = '';
        titleEl.innerText = 'Memuat...';
        
        const params = new URLSearchParams({ 
            type: type, 
            mode: state.mode || 'month', 
            startDate: state.startDate || '', 
            endDate: state.endDate || '' 
        });

        try {
            const res = await fetch(`{{ route('analytics.detail') }}?${params.toString()}`);
            
            // Error Handling: Cek HTTP Response Status
            if (!res.ok) {
                throw new Error(`HTTP Error: ${res.status} ${res.statusText}`);
            }
            
            const json = await res.json();
            
            // Error Handling: Cek apakah response valid
            if (json.error) {
                throw new Error(json.error);
            }
            
            titleEl.innerText = json.title || 'Detail Data';
            thead.innerHTML = (json.headers || []).map(h => `<th scope="col" class="px-5 py-3 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider bg-gray-100 border-b border-gray-200">${h}</th>`).join('');
            
            if(!json.rows || json.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="100%" class="px-5 py-8 text-center text-xs text-gray-500 italic bg-gray-50 rounded-b-lg"><svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Tidak ada data untuk periode ini.</td></tr>';
            } else {
                tbody.innerHTML = json.rows.map((row, idx) => {
                    const bgClass = idx % 2 === 0 ? 'bg-white' : 'bg-gray-50/50';
                    const cells = row.map(cell => `<td class="px-5 py-3 whitespace-nowrap text-xs text-gray-700 font-medium border-b border-gray-100">${cell ?? '-'}</td>`).join('');
                    return `<tr class="${bgClass} hover:bg-indigo-50/80 transition duration-150">${cells}</tr>`;
                }).join('');
            }
        } catch(err) { 
            console.error('Error loading detail:', err); 
            tbody.innerHTML = `<tr><td colspan="100%" class="text-red-500 p-8 text-center text-sm"><svg class="w-8 h-8 mx-auto mb-2 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><span class="font-bold block">Gagal Memuat Data</span><span class="text-xs text-gray-500 font-normal">${err.message || 'Terjadi kesalahan. Silakan coba lagi.'}</span></td></tr>`; 
            titleEl.innerText = 'Error';
        }
    }

    function closeDetailModal() { document.getElementById('detail-modal').classList.add('hidden'); }

    document.addEventListener('DOMContentLoaded', () => { Object.keys(chartConfig).forEach(key => { initChartState(key); reloadChart(key); }); });
</script>
@endsection
