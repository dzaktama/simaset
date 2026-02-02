<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMASET Vitech Asia' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- FONTS --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="h-full antialiased">

    <div class="min-h-screen relative flex">
        
        @include('partials.sidebar')

        <div id="main-content" class="flex-1 flex flex-col min-w-0 md:pl-64 transition-all duration-300">
            
            @include('partials.topbar')

            <main class="flex-1 py-8">
                <div class="w-full mx-auto px-4 sm:px-6 md:px-8">
                    
                    {{-- 1. ALERT SUKSES --}}
                    @if(session()->has('success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border-l-4 border-green-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-green-800">Berhasil</h3><p class="text-sm text-green-700 mt-1">{{ session('success') }}</p></div>
                        </div>
                    @endif

                    {{-- 2. ALERT ERROR (MODAL DARI ATAS) --}}
                    @if(session()->has('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                             x-transition:enter="transform ease-out duration-300 transition"
                             x-transition:enter-start="-translate-y-2 opacity-0 sm:-translate-y-0 sm:scale-95"
                             x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                             class="fixed top-4 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
                            <div class="pointer-events-auto w-full max-w-md bg-white rounded-lg shadow-2xl border-l-4 border-red-500 overflow-hidden ring-1 ring-black ring-opacity-5">
                                <div class="p-4 flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3 w-0 flex-1 pt-0.5">
                                        <p class="text-sm font-bold text-gray-900">Akses Ditolak!</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ session('error') }}</p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex">
                                        <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                            <span class="sr-only">Close</span>
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. ALERT ERROR VALIDASI FORM (MISAL: TANGGAL SALAH) --}}
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm animate-fade-in-down">
                            <div class="flex items-start">
                                <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <div>
                                    <h3 class="text-sm font-bold text-red-800">Terdapat Kesalahan Input</h3>
                                    <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session()->has('loginError'))
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500 shadow-sm flex items-start animate-fade-in-down">
                            <svg class="h-5 w-5 text-red-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div><h3 class="text-sm font-bold text-red-800">Error</h3><p class="text-sm text-red-700 mt-1">{{ session('loginError') }}</p></div>
                        </div>
                    @endif

                    @yield('container')
                    
                </div>
            </main>
        </div>
    </div>

    <form id="idle-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    <script>
        (function () {
            const IDLE_TIMEOUT = 15 * 60 * 1000;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let idleTimer = null;

            function resetTimer() {
                if (idleTimer) clearTimeout(idleTimer);
                idleTimer = setTimeout(doLogout, IDLE_TIMEOUT);
            }

            async function doLogout() {
                const form = document.getElementById('idle-logout-form');
                if (form) form.submit();
                else window.location.href = '/login';
            }

            ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'].forEach(evt => window.addEventListener(evt, resetTimer, true));
            resetTimer();
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>