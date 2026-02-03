<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMASET Vitech Asia' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
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
    </style>
    <script>
        window.currentUserId = {{ auth()->id() ?? 'null' }};
    </script>
</head>
<body class="h-full antialiased" x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }" x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">

    <div class="min-h-screen relative flex">
        
        {{-- Sidebar Wrapper with Alpine Binding --}}
        <div :class="sidebarOpen ? 'w-64' : 'w-0 -ml-0'" class="fixed inset-y-0 left-0 z-40 bg-white border-r border-gray-100 transition-all duration-300 md:block hidden overflow-hidden transform" id="sidebar-desktop">
             @include('partials.sidebar')
        </div>

        {{-- Mobile Sidebar (Off-canvas) --}}
        <div class="fixed inset-0 z-40 md:hidden hidden transition-all duration-300 transform" id="mobile-sidebar-container">
             {{-- Mobile Sidebar Logic handled separately or can be integrated --}}
             @include('partials.sidebar')
        </div>

        <div id="main-content" class="flex-1 flex flex-col min-w-0 transition-all duration-300" :class="sidebarOpen ? 'md:pl-64' : 'pl-0'">
            
            @include('partials.topbar')

            {{-- Main Content with padding-top to account for fixed header --}}
            <main class="flex-1 py-8 pt-20" id="page-container">
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

    {{-- SIMPLE SPA/PJAX NAVIGATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Intercept Sidebar & Tab Links
            document.body.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                // Only intercept internal links, not those with target="_blank" or specific ignores
                if (link && link.href && link.href.startsWith(window.location.origin) && !link.target && !link.getAttribute('download') && !link.dataset.noSpa) {
                    
                    e.preventDefault();
                    navigate(link.href);
                }
            });
            
            // Handle Back/Forward Browser Buttons
            window.addEventListener('popstate', (e) => {
                if(e.state && e.state.html) {
                    updateContent(e.state.html, document.title, false);
                } else {
                    location.reload(); // Fallback
                }
            });
        });

        async function navigate(url) {
            // Show Loading Indicator (Optional: Add NProgress here if wanted)
            const main = document.getElementById('page-container');
            main.style.opacity = '0.5';
            main.style.pointerEvents = 'none';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const html = await response.text();
                
                // Parse HTML to get Title and Container Content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('page-container').innerHTML;
                const title = doc.title;

                updateContent(newContent, title, true, url);
                
            } catch (error) {
                console.error('Navigation error:', error);
                window.location.href = url; // Fallback to full load
            } finally {
                main.style.opacity = '1';
                main.style.pointerEvents = 'auto';
            }
        }

        function updateContent(html, title, pushState = true, url = null) {
            const main = document.getElementById('page-container');
            
            // 1. Create a temporary container to parse the HTML and easily extract scripts
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // 2. Extract scripts BEFORE setting innerHTML (to avoid double execution issues or loss)
            const scripts = Array.from(tempDiv.querySelectorAll('script'));
            scripts.forEach(script => script.remove()); // Remove from temp to prevent auto-execution (though innerHTML normally doesn't exec scripts)
            
            // 3. Update DOM
            main.innerHTML = tempDiv.innerHTML;
            document.title = title;

            if (pushState && url) {
                history.pushState({ html: html, title: title }, title, url);
            }
            
            // 4. Re-execute Scripts
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                
                // Copy attributes
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                
                // Copy content
                newScript.textContent = oldScript.textContent;
                
                // Append to body (or main) to execute
                document.body.appendChild(newScript);
                
                // Optional: remove after execution to keep DOM clean? 
                // document.body.removeChild(newScript); 
            });

            // 5. Re-init Alpine
            if (window.Alpine) {
                window.Alpine.initTree(main);
            }

            // 6. Dispatch custom event for legacy/other scripts to hook into
            window.dispatchEvent(new Event('content:loaded'));
            
            // 7. Update Sidebar Active States
            updateActiveLinks(url || window.location.href);

            // 8. Update Tab System
            if(window.updateTabSystemWithUrl) window.updateTabSystemWithUrl(url || window.location.href, title);
        }

        function updateActiveLinks(url) {
            // Remove all active classes
            document.querySelectorAll('aside a').forEach(a => {
                a.classList.remove('bg-indigo-50', 'text-indigo-700', 'border-l-4', 'border-indigo-600');
                a.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-indigo-600');
                
                // Reset Icons
                const icon = a.querySelector('svg');
                if(icon) {
                    icon.classList.remove('text-indigo-600');
                    icon.classList.add('text-gray-400');
                }

                if (a.href === url) {
                    // Set Active
                    a.classList.add('bg-indigo-50', 'text-indigo-700', 'border-l-4', 'border-indigo-600');
                    a.classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-indigo-600');
                     // Active Icon
                    if(icon) {
                        icon.classList.add('text-indigo-600');
                        icon.classList.remove('text-gray-400');
                    }
                }
            });
        }
    </script>