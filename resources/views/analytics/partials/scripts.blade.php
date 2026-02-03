<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartInstances = {};
    const chartConfig = {
        borrowingTrend: { type: 'line', multiAxis: true, smooth: true }, // Indigo
        maintenanceCost: { type: 'line', color: 'rgb(249, 115, 22)', fill: true, smooth: true }, // Orange
        topUsers: { type: 'bar', indexAxis: 'y', color: 'rgb(16, 185, 129)' }, // Emerald
        returnCompliance: { type: 'doughnut', cutout: '70%' },
        departmentDist: { type: 'pie' },
        ticketStats: { type: 'doughnut', cutout: '60%' },
        topAssets: { type: 'bar', indexAxis: 'y', color: 'rgb(236, 72, 153)' }, // Pink
        purchaseTrend: { type: 'line', color: 'rgb(59, 130, 246)', fill: false, smooth: true }, // Blue
        assetReliability: { type: 'bar', color: 'rgb(239, 68, 68)' }, // Red
        assetAging: { type: 'bar', color: 'rgb(107, 114, 128)' }, // Gray
    };

    const chartState = {};

    function initChartState(id) {
        if(!chartState[id]) {
            chartState[id] = { mode: 'month' };
        }
    }

    // --- GLOBAL DATE LOGIC ---
    function setPreset(preset) {
        const startInput = document.getElementById('global-start-date');
        const endInput = document.getElementById('global-end-date');
        const now = new Date();
        let start, end;

        // Reset times for cleaner calculations
        now.setHours(23, 59, 59, 999);
        end = new Date(now);

        switch(preset) {
            case '1D':
                start = new Date(now);
                break;
            case '5D':
                start = new Date(now);
                start.setDate(now.getDate() - 5);
                break;
            case '1M':
                start = new Date(now);
                start.setMonth(now.getMonth() - 1);
                break;
            case '6M':
                start = new Date(now);
                start.setMonth(now.getMonth() - 6);
                break;
            case 'YTD':
                start = new Date(now.getFullYear(), 0, 1);
                break;
            case '1Y':
                start = new Date(now);
                start.setFullYear(now.getFullYear() - 1);
                break;
            case '5Y':
                start = new Date(now);
                start.setFullYear(now.getFullYear() - 5);
                break;
            case 'ALL':
                start = new Date('2020-01-01'); // Assuming app start or sufficient past
                break;
            default: // month as default toggle
                start = new Date(now.getFullYear(), now.getMonth(), 1);
        }

        // Format to YYYY-MM-DD manually
        const formatDate = (date) => {
            const offset = date.getTimezoneOffset();
            date = new Date(date.getTime() - (offset*60*1000));
            return date.toISOString().split('T')[0];
        }

        if(startInput && endInput) {
            startInput.value = formatDate(start);
            endInput.value = formatDate(end);
            applyDateFilter();
            updateActiveButton(preset);
        }
    }

    function updateActiveButton(activeId) {
        const buttons = document.querySelectorAll('.preset-btn');
        buttons.forEach(btn => {
            // Reset to default style
            btn.classList.remove('bg-indigo-100', 'text-indigo-700');
            btn.classList.add('text-gray-500', 'hover:bg-gray-50', 'hover:text-indigo-600');
            
            // Check based on ID
            if (btn.id === `btn-${activeId}`) {
                 btn.classList.remove('text-gray-500', 'hover:bg-gray-50', 'hover:text-indigo-600');
                 btn.classList.add('bg-indigo-100', 'text-indigo-700');
            }
        });
    }

    function applyDateFilter() {
        const startDate = document.getElementById('global-start-date').value;
        const endDate = document.getElementById('global-end-date').value;

        // Auto-detect mode based on range
        let mode = 'month';
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            
            // Jika range < 60 hari, tampilkan per Hari. Jika lebih, per Bulan.
            if (diffDays <= 60) {
                mode = 'day';
            }
        }
        
        console.log(`Filtering: ${startDate} to ${endDate} (Auto Mode: ${mode})`);

        // Reload all charts
        Object.keys(chartConfig).forEach(key => {
            updateChart(key, mode, startDate, endDate);
        });
    }

    function updateChart(id, mode, startDate = null, endDate = null) {
        chartState[id].mode = mode;
        chartState[id].startDate = startDate;
        chartState[id].endDate = endDate;
        reloadChart(id);
    }

    async function reloadChart(key) {
        const config = chartConfig[key];
        const state = chartState[key];
        const canvas = document.getElementById(`chart-${key}`);
        
        if (!canvas) return;
        
        // Simple Loading
        if(chartInstances[key]) chartInstances[key].destroy();

        // Query params including Global Dates
        const params = new URLSearchParams({ 
            type: key, 
            mode: state.mode || 'month',
            startDate: state.startDate || '',
            endDate: state.endDate || ''
        });
        
        try {
            const res = await fetch(`{{ route('analytics.data') }}?${params.toString()}`);
            const json = await res.json();
            
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 10 },
                plugins: {
                    legend: { 
                        position: ['doughnut', 'pie'].includes(config.type) ? 'right' : 'bottom',
                        labels: { font: { size: 10, family: 'Inter', weight: '600' }, usePointStyle: true, boxWidth: 6 }
                    },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { 
                        display: !['doughnut', 'pie'].includes(config.type),
                        grid: { display: false },
                        ticks: { font: { size: 9 }, color: '#9ca3af' }
                    },
                    y: { 
                        display: !['doughnut', 'pie'].includes(config.type),
                        grid: { borderDash: [4, 4], color: '#f3f4f6' },
                        ticks: { font: { size: 9 }, color: '#9ca3af' } 
                    }
                }
            };

            // PROPER Multi Axis Overrides
            if (config.multiAxis) {
                options.scales.y = {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { borderDash: [4, 4], color: '#f3f4f6' },
                    ticks: { font: { size: 9 }, color: '#9ca3af' }
                };
                options.scales.y1 = {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }, 
                    ticks: { font: { size: 9 }, color: '#9ca3af' }
                };
                options.interaction = {
                    mode: 'index',
                    intersect: false,
                };
            }

            // End of options override
            const _dummy = {
            };
            
            // Dataset Construction
            let datasets;
            if (json.datasets) {
                datasets = json.datasets; // Multi series
            } else {
                 const color = config.color || '#6366f1';
                 const bgColor = config.fill ? color.replace('rgb', 'rgba').replace(')', ', 0.1)') : color;
                 
                 datasets = [{
                    label: json.label || 'Data',
                    data: json.data || [],
                    backgroundColor: ['doughnut', 'pie'].includes(config.type) ? ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] : bgColor,
                    borderColor: ['doughnut', 'pie'].includes(config.type) ? '#ffffff' : color,
                    borderWidth: 2,
                    fill: config.fill || false,
                    tension: config.smooth ? 0.4 : 0,
                    borderRadius: 4
                }];
            }
            
            chartInstances[key] = new Chart(canvas, {
                type: config.type,
                data: { labels: json.labels || [], datasets: datasets },
                options: options
            });
            
        } catch (e) {
            console.error(e);
        }
    }

    async function openDetail(type) {
        const modal = document.getElementById('detail-modal');
        const titleEl = document.getElementById('modal-title');
        const thead = document.getElementById('modal-thead-tr');
        const tbody = document.getElementById('modal-tbody');
        const state = chartState[type] || {};
            
        modal.classList.remove('hidden');
        titleEl.innerText = 'Memuat Data...';
        tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-gray-400">Loading...</td></tr>';
        
        try {
            const params = new URLSearchParams({ 
                type: type, 
                mode: state.mode || 'month', 
                startDate: state.startDate || '', 
                endDate: state.endDate || '' // Pass date filter to detail modal too
            });

            const res = await fetch(`{{ route('analytics.detail') }}?${params.toString()}`);
            const json = await res.json();
            
            titleEl.innerText = json.title;
            thead.innerHTML = json.headers.map(h => `<th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">${h}</th>`).join('');
            
            if(!json.rows.length) {
                tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-gray-400 italic">Tidak ada data.</td></tr>';
            } else {
                tbody.innerHTML = json.rows.map(row => `
                    <tr class="hover:bg-gray-50 transition">
                        ${row.map(c => `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${c}</td>`).join('')}
                    </tr>
                `).join('');
            }
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="100%" class="p-8 text-center text-red-500">Gagal memuat data.</td></tr>';
        }
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    function initDashboardCharts() {
        Object.keys(chartConfig).forEach(key => {
            initChartState(key);
        });
        setPreset('month'); 
    }

    // Run on initial load
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
    
    // Run on PJAX content load
    window.addEventListener('content:loaded', initDashboardCharts);
</script>
