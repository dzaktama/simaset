<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMASET Vitech Asia' }}</title>
    {{-- CSRF token buat dipake di JS kalau perlu (fetch/ajax) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom Scrollbar untuk Sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="h-full antialiased">

    <div class="min-h-screen relative flex">
        
        {{-- Sidebar Include --}}
        @include('partials.sidebar')

        {{-- Main Content Wrapper --}}
        {{-- md:pl-64 PENTING: Memberi ruang untuk sidebar fixed di desktop --}}
        <div class="flex-1 flex flex-col min-w-0 md:pl-64 transition-all duration-300">
            
            {{-- Topbar Include --}}
            @include('partials.topbar')

            {{-- Konten Utama --}}
            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    
                    {{-- Flash Messages --}}
                    @if(session()->has('success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border-l-4 border-green-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-green-800">Berhasil</h3><p class="text-sm text-green-700 mt-1">{{ session('success') }}</p></div>
                        </div>
                    @endif

                    @if(session()->has('loginError'))
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-red-800">Error</h3><p class="text-sm text-red-700 mt-1">{{ session('loginError') }}</p></div>
                        </div>
                    @endif

                    {{-- Isi Halaman --}}
                    @yield('container')
                    
                </div>
            </main>
        </div>
    </div>

    
    <form id="idle-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    {{-- Script idle-logout: versi lebih aman pake fetch + CSRF header, ada fallback kalau error --}}
    <script>
        (function () {
            // Timeout default: 5 menit (ubah kalo mau testing cepat)
            const IDLE_TIMEOUT = 5 * 60 * 1000; // ms
            // Untuk test cepat, bisa ubah ke 15 * 1000 (15 detik)

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let idleTimer = null;

            function resetTimer() {
                if (idleTimer) clearTimeout(idleTimer);
                idleTimer = setTimeout(doLogout, IDLE_TIMEOUT);
            }

            async function doLogout() {
                // Kita coba POST via fetch (supaya bisa tangani response 419),
                // sertakan credentials supaya cookie session dikirim.
                try {
                    const resp = await fetch('{{ route('logout') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({})
                    });

                    // Kalau berhasil atau redirect, pindah ke login
                    if (resp.ok) {
                        // berhasil logout, redirect ke login
                        window.location.href = '/login';
                        return;
                    }

                    // Kalau server balik 419 (Page Expired) atau bukan ok, fallback ke GET login
                    if (resp.status === 419 || resp.status === 403) {
                        window.location.href = '/login';
                        return;
                    }

                    // Fallback umum: submit form (ini akan pakai CSRF hidden input)
                    const form = document.getElementById('idle-logout-form');
                    if (form) form.submit();
                    else window.location.href = '/login';
                } catch (err) {
                    // Kalau fetch error (network/CSRF expired), fallback ke redirect login
                    console.warn('Auto-logout: fetch error, redirecting to login', err);
                    window.location.href = '/login';
                }
            }

            // Event yang dianggap aktivitas user, reset timer kalau ada.
            const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];
            activityEvents.forEach(evt => window.addEventListener(evt, resetTimer, true));

            // Start timer pas halaman dibuka
            resetTimer();
        })();
    </script>

            {{-- Chart.js CDN (dipakai di dashboard) --}}
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
