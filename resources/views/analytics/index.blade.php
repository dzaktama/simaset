@extends('layouts.main')

@section('container')
<div class="space-y-6">
    
    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pusat Analisis Data</h2>
            <p class="text-sm text-gray-600">Monitoring performa aset dan maintenance secara real-time.</p>
        </div>
        
        {{-- Global Filter --}}
        <div class="flex items-center gap-2 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
            <button onclick="setGlobalRange('month')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-indigo-700 bg-indigo-50 hover:bg-indigo-100" id="btn-month">Bulanan</button>
            <button onclick="setGlobalRange('year')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-600 hover:bg-gray-50" id="btn-year">Tahunan</button>
        </div>
    </div>

    {{-- Chart Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        {{-- 1. Biaya Maintenance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80 col-span-1 md:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-gray-800">💰 Biaya Maintenance</h3>
                <button class="text-xs text-indigo-600 hover:underline">Detail</button>
            </div>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-maintenanceCost"></canvas>
            </div>
        </div>

        {{-- 2. Kepatuhan Pengembalian --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">⏰ Kepatuhan Pengembalian</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-returnCompliance"></canvas>
            </div>
        </div>

        {{-- 3. Aset Paling Sering Rusak --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🛠️ Sering Rusak (Kategori)</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-assetReliability"></canvas>
            </div>
        </div>

        {{-- 4. Top Aset Dipinjam --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80 col-span-1 md:col-span-2">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🏆 Aset Paling Laris</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-topAssets"></canvas>
            </div>
        </div>

        {{-- 5. Status Tiket --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🎫 Status Tiket</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-ticketStats"></canvas>
            </div>
        </div>

        {{-- 6. Umur Aset --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">👴 Umur Aset (Aging)</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-assetAging"></canvas>
            </div>
        </div>

         {{-- 7. Distribusi Dept --}}
         <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🏢 Distribusi Dept</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-departmentDist"></canvas>
            </div>
        </div>

         {{-- 8. User Paling Aktif --}}
         <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🙋‍♂️ User Teraktif</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-topUsers"></canvas>
            </div>
        </div>

         {{-- 9. Tren Pembelian --}}
         <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col h-80 col-span-1 md:col-span-2">
            <h3 class="text-base font-semibold text-gray-800 mb-4">🛒 Tren Pembelian Tahunan</h3>
            <div class="flex-1 relative w-full h-full min-h-0">
                <canvas id="chart-purchaseTrend"></canvas>
            </div>
        </div>
        
    </div>

    {{-- DETAIL MODAL --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Detail Data</h3>
                            <div class="mt-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200" id="modal-table">
                                        <thead class="bg-gray-50">
                                            <tr id="modal-thead-tr">
                                                {{-- Dynamic Headers --}}
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="modal-tbody">
                                            {{-- Dynamic Rows --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDetailModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let globalRange = 'year';
    const chartInstances = {};

    const chartConfig = {
        maintenanceCost: { type: 'line', color: 'rgb(79, 70, 229)', fill: true },
        topAssets: { type: 'bar', indexAxis: 'y', color: 'rgb(16, 185, 129)' },
        returnCompliance: { type: 'doughnut' },
        assetReliability: { type: 'bar', color: 'rgb(239, 68, 68)' },
        departmentDist: { type: 'pie' },
        assetAging: { type: 'bar', color: 'rgb(245, 158, 11)' },
        ticketStats: { type: 'bar', color: 'rgb(59, 130, 246)' },
        topUsers: { type: 'bar', indexAxis: 'y', color: 'rgb(139, 92, 246)' },
        purchaseTrend: { type: 'line', color: 'rgb(14, 165, 233)' },
    };

    function setGlobalRange(range) {
        globalRange = range;
        
        ['month', 'year'].forEach(r => {
            const btn = document.getElementById(`btn-${r}`);
            if(r === range) {
                btn.classList.add('bg-indigo-50', 'text-indigo-700');
                btn.classList.remove('text-gray-600', 'hover:bg-gray-50');
            } else {
                btn.classList.remove('bg-indigo-50', 'text-indigo-700');
                btn.classList.add('text-gray-600', 'hover:bg-gray-50');
            }
        });

        loadAllCharts();
    }

    async function loadAllCharts() {
        for (const [key, config] of Object.entries(chartConfig)) {
            await fetchAndRenderChart(key, config);
        }
    }

    async function fetchAndRenderChart(key, config) {
        const ctx = document.getElementById(`chart-${key}`);
        if (!ctx) return;

        // Bind Detail Button
        // We assume the button is in the same container, or we find it by ID pattern if possible
        // Ideally, we should have given IDs to the buttons.
        // Let's find the button relative to the canvas container
        const container = ctx.closest('.bg-white');
        const detailBtn = container.querySelector('button');
        if(detailBtn) {
            detailBtn.onclick = () => openDetail(key);
        }

        try {
            const response = await fetch(`{{ route('analytics.data') }}?type=${key}&range=${globalRange}`);
            const json = await response.json();

            if (chartInstances[key]) {
                chartInstances[key].destroy();
            }

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (e) => openDetail(key), // Also open detail on chart click
                plugins: {
                    legend: { display: ['doughnut', 'pie'].includes(config.type), position: 'bottom' }
                }
            };
            
            let datasets = [{
                label: json.label || 'Data',
                data: json.data,
                backgroundColor: json.colors || (config.type === 'line' ? config.color.replace('rgb', 'rgba').replace(')', ', 0.1)') : config.color),
                borderColor: config.color,
                borderWidth: 1,
                fill: config.fill || false,
                tension: 0.4
            }];
            
            if(['pie', 'doughnut'].includes(config.type) && !json.colors) {
                datasets[0].backgroundColor = [
                   '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#6366F1', '#8B5CF6', '#EC4899'
                ];
            }

            chartInstances[key] = new Chart(ctx, {
                type: config.type,
                data: {
                    labels: json.labels,
                    datasets: datasets
                },
                options: options
            });

        } catch (err) {
            console.error(`Failed to load chart ${key}:`, err);
        }
    }

    async function openDetail(type) {
        // Show Loading state?
        const modal = document.getElementById('detail-modal');
        const titleEl = document.getElementById('modal-title');
        const thead = document.getElementById('modal-thead-tr');
        const tbody = document.getElementById('modal-tbody');
        
        modal.classList.remove('hidden');
        titleEl.innerText = 'Memuat Data...';
        tbody.innerHTML = '<tr><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Loading...</td></tr>';

        try {
            const res = await fetch(`{{ route('analytics.detail') }}?type=${type}&range=${globalRange}`);
            const json = await res.json();

            titleEl.innerText = json.title;
            
            // Build Headers
            thead.innerHTML = json.headers.map(h => `<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${h}</th>`).join('');
            
            // Build Rows
            if(json.rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="100%" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data.</td></tr>';
            } else {
                tbody.innerHTML = json.rows.map(row => {
                    const cells = row.map(cell => `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${cell}</td>`).join('');
                    return `<tr>${cells}</tr>`;
                }).join('');
            }

        } catch(err) {
            console.error(err);
            titleEl.innerText = 'Error';
            tbody.innerHTML = '<tr><td class="text-red-500 p-4">Gagal memuat data detail.</td></tr>';
        }
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        loadAllCharts();
    });

</script>
@endsection
