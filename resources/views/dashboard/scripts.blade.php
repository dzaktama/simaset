{{-- [PERF] Chart.js dimuat sekali di layout (versi dipin + defer), tidak lagi dobel di sini --}}
<script>
    // 1. Sidebar & Utility
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    var sidebar = document.getElementById('sidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    var openSidebarBtn = document.getElementById('openSidebar');
    var closeSidebarBtn = document.getElementById('closeSidebar');

    function toggleSidebar() {
        if(sidebar) sidebar.classList.toggle('-translate-x-full');
        if(sidebarOverlay) sidebarOverlay.classList.toggle('hidden');
    }

    if (openSidebarBtn) openSidebarBtn.addEventListener('click', toggleSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

    // 2. Modal Detail Request
    function openRequestDetail(req, asset, user) {
        document.getElementById('modalAssetName').innerText = asset.name;
        document.getElementById('modalAssetSN').innerText = asset.serial_number;
        document.getElementById('modalAssetStatus').innerText = asset.status.toUpperCase();
        document.getElementById('modalAssetCondition').innerText = asset.condition_notes || 'Tidak ada catatan kondisi.';
        document.getElementById('modalUserName').innerText = user.name;
        document.getElementById('modalUserEmail').innerText = user.email;
        document.getElementById('modalUserInitials').innerText = user.name.charAt(0);
        document.getElementById('modalReason').innerText = req.reason;
        
        const dateOpts = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
        const reqDate = new Date(req.created_at).toLocaleDateString('id-ID', dateOpts) + ' WIB';
        const retDate = req.return_date ? new Date(req.return_date).toLocaleDateString('id-ID', dateOpts) : 'Tidak ditentukan';
        document.getElementById('modalReqDate').innerText = reqDate;
        document.getElementById('modalReturnDate').innerText = retDate;

        document.getElementById('requestModal').classList.remove('hidden');
    }
    function closeRequestModal() { document.getElementById('requestModal').classList.add('hidden'); }

    // 3. Modal Tolak (Reject) - [BAGIAN YANG DIPERBAIKI]
    function openRejectModal(id, userName, assetName) {
        document.getElementById('rejectUserName').innerText = userName;
        document.getElementById('rejectAssetName').innerText = assetName;
        
        // PERBAIKAN: Menggunakan window.location.origin dan route 'borrowing'
        // Sebelumnya: /requests/${id}/reject (Salah)
        // Sekarang: /borrowing/${id}/reject (Benar)
        const baseUrl = window.location.origin;
        document.getElementById('rejectForm').action = `${baseUrl}/borrowing/${id}/reject`; 
        
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    // 4. Modal Verifikasi Pengembalian
    function openVerifyModal(retData, assetData, userData) {
        document.getElementById('verifyUserName').innerText = userData.name;
        document.getElementById('verifyDate').innerText = retData.return_date; 
        document.getElementById('verifyAssetName').innerText = assetData.name;
        document.getElementById('verifyAssetSN').innerText = assetData.serial_number;
        document.getElementById('verifyUserNotes').innerText = retData.notes || 'Tidak ada catatan user.';
        
        const badge = document.getElementById('verifyUserConditionBadge');
        if (retData.condition === 'good') {
            badge.innerText = 'USER: BAIK';
            badge.className = 'px-2 py-0.5 text-xs font-bold uppercase rounded-full border bg-green-100 text-green-800 border-green-200';
        } else {
            badge.innerText = 'USER: RUSAK/BERMASALAH';
            badge.className = 'px-2 py-0.5 text-xs font-bold uppercase rounded-full border bg-red-100 text-red-800 border-red-200';
        }

        // Opsional: Gunakan base URL juga biar aman
        const baseUrl = window.location.origin;
        document.getElementById('verifyForm').action = `${baseUrl}/returns/${retData.id}/verify`;
        
        document.getElementById('verifyModal').classList.remove('hidden');
    }
    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('hidden');
    }

    // 5. Jam Digital (DIHAPUS - Pindah ke Topbar Global)

    // 6. Chart Logic (KODE ASLI ANDA - TIDAK DIUBAH)
    @if(in_array(session('impersonate_role', auth()->user()->role?->slug), ['admin', 'super_admin']))
    (function(){
        const borrowCanvas = document.getElementById('borrowTrendChart');
        const assetCanvas = document.getElementById('assetAdditionChart');
        const pieCanvas = document.getElementById('assetsStatusPie');
        
        if (!borrowCanvas || !assetCanvas || !pieCanvas) return;

        const borrowCtx = borrowCanvas.getContext('2d');
        const assetCtx = assetCanvas.getContext('2d');
        const pieCtx = pieCanvas.getContext('2d');

        let borrowChart = null;
        let assetChart = null;
        let pieChart = null;
        let currentSlide = 0;

        const slides = [
            { id: 0, title: 'Tren Peminjaman Aset', description: 'Menampilkan jumlah aset yang diminta per periode' },
            { id: 1, title: 'Tren Penambahan Aset', description: 'Menampilkan pertambahan aset yang didaftarkan per periode' }
        ];

        function updateCarousel() {
            document.getElementById('chartTitle').innerText = slides[currentSlide].title;
            document.getElementById('chartDescription').innerText = slides[currentSlide].description;
            document.querySelectorAll('[id^="dot"]').forEach((dot, i) => {
                if (i === currentSlide) {
                    dot.classList.add('!bg-white'); dot.classList.remove('bg-white/40');
                } else {
                    dot.classList.remove('!bg-white'); dot.classList.add('bg-white/40');
                }
            });
            document.getElementById('chartSlide0').style.opacity = currentSlide === 0 ? '1' : '0';
            document.getElementById('chartSlide0').style.transform = currentSlide === 0 ? 'translateX(0)' : 'translateX(-100%)';
            document.getElementById('chartSlide1').style.opacity = currentSlide === 1 ? '1' : '0';
            document.getElementById('chartSlide1').style.transform = currentSlide === 1 ? 'translateX(0)' : 'translateX(100%)';
        }

        document.getElementById('nextChart').addEventListener('click', () => { currentSlide = (currentSlide + 1) % slides.length; updateCarousel(); });
        document.getElementById('prevChart').addEventListener('click', () => { currentSlide = (currentSlide - 1 + slides.length) % slides.length; updateCarousel(); });
        document.getElementById('dot0').addEventListener('click', () => { currentSlide = 0; updateCarousel(); });
        document.getElementById('dot1').addEventListener('click', () => { currentSlide = 1; updateCarousel(); });

        // [PERF] Cache hasil per range di sisi klien + data awal dari server
        const chartCache = {};
        @isset($initialCharts)
        chartCache.monthly = @json($initialCharts);
        @endisset
        let pendingController = null;

        // Chart.js dimuat dengan `defer`; tunggu sampai siap tanpa memblokir render halaman
        function whenChartReady() {
            if (window.Chart) return Promise.resolve();
            return new Promise(resolve => {
                const check = () => window.Chart ? resolve() : setTimeout(check, 30);
                check();
            });
        }

        async function fetchRange(range) {
            if (pendingController) pendingController.abort();
            pendingController = new AbortController();
            const opts = { signal: pendingController.signal, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
            // Fetch secara bersamaan (Concurrent)
            const [borrowRes, assetRes] = await Promise.all([
                fetch(`/charts/borrow-stats?range=${range}`, opts),
                fetch(`/charts/asset-stats?range=${range}`, opts)
            ]);
            if (!borrowRes.ok || !assetRes.ok) throw new Error('HTTP ' + borrowRes.status + '/' + assetRes.status);
            return { borrow: await borrowRes.json(), asset: await assetRes.json() };
        }

        function renderCharts(data) {
            const borrowJson = data.borrow;
            const assetJson = data.asset;
            const chartOptions = {
                responsive: true, 
                maintainAspectRatio: false, 
                animation: { duration: 300 },
                interaction: { mode: 'index', intersect: false }, 
                plugins: { legend: { position: 'bottom' } }, 
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } 
            };

            // Update data chart yang sudah ada (lebih ringan daripada destroy + create ulang)
            if (borrowChart) {
                borrowChart.data.labels = borrowJson.series.labels;
                borrowChart.data.datasets[0].data = borrowJson.series.approved;
                borrowChart.data.datasets[1].data = borrowJson.series.rejected;
                borrowChart.update();
            } else {
                Chart.getChart(borrowCanvas)?.destroy();
                borrowChart = new Chart(borrowCtx, {
                    type: 'line',
                    data: {
                        labels: borrowJson.series.labels,
                        datasets: [
                            { label: 'Disetujui', data: borrowJson.series.approved, fill: true, backgroundColor: 'rgba(34,197,94,0.1)', borderColor: 'rgba(34,197,94,1)', borderWidth: 2.5, tension: 0.4 },
                            { label: 'Ditolak', data: borrowJson.series.rejected, fill: true, backgroundColor: 'rgba(239,68,68,0.1)', borderColor: 'rgba(239,68,68,1)', borderWidth: 2.5, tension: 0.4 }
                        ]
                    },
                    options: chartOptions
                });
            }

            if (assetChart) {
                assetChart.data.labels = assetJson.series.labels;
                assetChart.data.datasets[0].data = assetJson.series.data;
                assetChart.update();
            } else {
                Chart.getChart(assetCanvas)?.destroy();
                assetChart = new Chart(assetCtx, {
                    type: 'line',
                    data: {
                        labels: assetJson.series.labels,
                        datasets: [{ label: 'Aset Ditambahkan', data: assetJson.series.data, fill: true, backgroundColor: 'rgba(34,197,94,0.1)', borderColor: 'rgba(34,197,94,1)', borderWidth: 2.5, tension: 0.4 }]
                    },
                    options: { ...chartOptions, plugins: { legend: { display: false } } }
                });
            }

            const sc = assetJson.statusCounts;
            const pieData = [sc.available||0, sc.deployed||0, sc.maintenance||0, sc.broken||0];
            if (pieChart) {
                pieChart.data.datasets[0].data = pieData;
                pieChart.update();
            } else {
                Chart.getChart(pieCanvas)?.destroy();
                pieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available','Deployed','Maintenance','Broken'],
                        datasets: [{ data: pieData, backgroundColor: ['#10B981','#3B82F6','#F59E0B','#EF4444'], borderWidth: 3 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, animation: { duration: 300 }, plugins: { legend: { position: 'top' } } }
                });
            }
        }

        let currentRange = 'monthly';
        async function loadCharts(range = currentRange, force = false){
            currentRange = range;
            const loading = document.getElementById('chartLoading');
            const cached = !force && chartCache[range];
            if(loading && !cached) loading.classList.remove('hidden');

            try{
                const data = cached || await fetchRange(range);
                chartCache[range] = data;
                await whenChartReady();
                if (range !== currentRange) return; // user sudah pindah range
                renderCharts(data);
            }catch(err){ 
                if (err.name !== 'AbortError') console.error('Gagal load chart data:', err); 
            } finally {
                if(loading && range === currentRange) loading.classList.add('hidden');
            }
        }

        // Tombol refresh memaksa ambil data terbaru dari server
        window.dashboardLoadCharts = (range) => loadCharts(range || currentRange, true);

        loadCharts('monthly');

        document.querySelectorAll('.range-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.range-btn').forEach(b => { b.classList.remove('bg-indigo-100', 'border-indigo-300', 'text-indigo-700', 'font-bold'); b.classList.add('bg-white', 'border-gray-300', 'text-gray-700'); });
                e.currentTarget.classList.add('bg-indigo-100', 'border-indigo-300', 'text-indigo-700', 'font-bold');
                loadCharts(e.currentTarget.getAttribute('data-range'));
            });
        });
    })();
    @endif
</script>