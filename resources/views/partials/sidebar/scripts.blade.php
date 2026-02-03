    // Constants
    const SECTION_IDS = ['master', 'transaksi', 'laporan', 'utilitas'];
    
    // Dynamic Tooltip Logic
    let tooltipEl = null;
    let activeTooltipHandler = null;

    const tooltipContent = {
        'master': '<strong class="block mb-1 text-indigo-200">MASTER DATA</strong>Dashboard, Gudang & Katalog Aset.',
        'transaksi': '<strong class="block mb-1 text-indigo-200">TRANSAKSI</strong>Peminjaman, Pengembalian, Mutasi & Lapor Kerusakan.',
        'laporan': '<strong class="block mb-1 text-indigo-200">LAPORAN</strong>Pusat Data Statistik & Audit Aset.',
        'utilitas': '<strong class="block mb-1 text-indigo-200">UTILITAS</strong>Manajemen User, Grup & Pengaturan Sistem.',
        'bantuan': '<strong class="block mb-1 text-indigo-200">BANTUAN</strong>Panduan Penggunaan Aplikasi.'
    };

    function startTooltip(event, sectionId) {
        // Create if not exists
        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.id = 'sidebar-tooltip';
            tooltipEl.className = 'fixed z-50 px-3 py-2 text-xs font-medium text-white bg-gray-900/90 backdrop-blur rounded-lg shadow-xl pointer-events-none transition-opacity duration-150 opacity-0 max-w-[200px] leading-relaxed border border-white/10';
            document.body.appendChild(tooltipEl);
        }

        // Set Content
        tooltipEl.innerHTML = tooltipContent[sectionId] || 'Info Menu';
        tooltipEl.style.opacity = '1';

        // Initial Position
        moveTooltip(event);

        // Attach Listener
        event.currentTarget.addEventListener('mousemove', moveTooltip);
    }

    function moveTooltip(event) {
        if (!tooltipEl) return;
        
        // Offset from cursor
        const offset = 15;
        let left = event.clientX + offset;
        let top = event.clientY + offset;

        // Boundary Check (Prevent going off screen)
        if (left + 200 > window.innerWidth) left = event.clientX - 215;
        if (top + 100 > window.innerHeight) top = event.clientY - 100;

        tooltipEl.style.left = left + 'px';
        tooltipEl.style.top = top + 'px';
    }

    function stopTooltip(event) {
        if (tooltipEl) {
            tooltipEl.style.opacity = '0';
        }
        if (event && event.currentTarget) {
            event.currentTarget.removeEventListener('mousemove', moveTooltip);
        }
    }

    // Toggle Sidebar Menu handled via Alpine.js x-data in each menu file.
    // This script now only handles legacy or global overrides if needed.

    // Global Mobile Sidebar Toggle (AlpineJS helper)
    window.toggleMobile = function() {
        const mobileContainer = document.getElementById('mobile-sidebar-container');
        if(mobileContainer) {
            // Kita gunakan Dispatch Event ke Alpine atau manipulasi class manual
            // Paling aman manipulasi class 'hidden' jika tidak pakai x-show
            if(mobileContainer.classList.contains('hidden')) {
                mobileContainer.classList.remove('hidden');
            } else {
                mobileContainer.classList.add('hidden');
            }
        }
    }
    // Alias for backward compatibility (used in topbar/sidebar header)
    window.toggleMobileSidebar = window.toggleMobile;
</script>
