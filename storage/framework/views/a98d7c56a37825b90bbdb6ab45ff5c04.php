<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

    // Modal Detail Request (Admin)
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

    // Modal Tolak (Reject)
    function openRejectModal(id, userName, assetName) {
        document.getElementById('rejectUserName').innerText = userName;
        document.getElementById('rejectAssetName').innerText = assetName;
        document.getElementById('rejectForm').action = `/requests/${id}/reject`; 
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); }

    // Modal Verifikasi Pengembalian
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

        document.getElementById('verifyForm').action = `/returns/${retData.id}/verify`;
        document.getElementById('verifyModal').classList.remove('hidden');
    }
    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('hidden');
    }

    // Jam Digital
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }).replace(/\./g, ':');
        const clockEl = document.getElementById('live-clock');
        if(clockEl) clockEl.innerText = timeString;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart Logic (Hanya untuk Admin)
    <?php if(auth()->user()->role === 'admin'): ?>
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
            { id: 0, title: '📊 Tren Peminjaman Aset', description: 'Menampilkan jumlah aset yang diminta per periode' },
            { id: 1, title: '📈 Tren Penambahan Aset', description: 'Menampilkan pertambahan aset yang didaftarkan per periode' }
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

        async function loadCharts(range = 'monthly'){
            try{
                const borrowRes = await fetch(`/charts/borrow-stats?range=${range}`);
                const borrowJson = await borrowRes.json();
                const assetRes = await fetch(`/charts/asset-stats?range=${range}`);
                const assetJson = await assetRes.json();

                // Chart 1
                if (borrowChart) borrowChart.destroy();
                borrowChart = new Chart(borrowCtx, {
                    type: 'line',
                    data: {
                        labels: borrowJson.series.labels,
                        datasets: [
                            { label: '✅ Disetujui', data: borrowJson.series.approved, fill: true, backgroundColor: 'rgba(34,197,94,0.1)', borderColor: 'rgba(34,197,94,1)', borderWidth: 2.5, tension: 0.4 },
                            { label: '❌ Ditolak', data: borrowJson.series.rejected, fill: true, backgroundColor: 'rgba(239,68,68,0.1)', borderColor: 'rgba(239,68,68,1)', borderWidth: 2.5, tension: 0.4 }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                });

                // Chart 2
                if (assetChart) assetChart.destroy();
                assetChart = new Chart(assetCtx, {
                    type: 'line',
                    data: {
                        labels: assetJson.series.labels,
                        datasets: [{ label: 'Aset Ditambahkan', data: assetJson.series.data, fill: true, backgroundColor: 'rgba(34,197,94,0.1)', borderColor: 'rgba(34,197,94,1)', borderWidth: 2.5, tension: 0.4 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                });

                // Pie Chart
                const sc = assetJson.statusCounts;
                if (pieChart) pieChart.destroy();
                pieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available','Deployed','Maintenance','Broken'],
                        datasets: [{ data: [sc.available||0, sc.deployed||0, sc.maintenance||0, sc.broken||0], backgroundColor: ['#10B981','#3B82F6','#F59E0B','#EF4444'], borderWidth: 3 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
                });

            }catch(err){ console.error('Gagal load chart data:', err); }
        }

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
    <?php endif; ?>
</script><?php /**PATH C:\laragon\www\simaset_fix\resources\views/dashboard/scripts.blade.php ENDPATH**/ ?>