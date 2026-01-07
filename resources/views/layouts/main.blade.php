<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIMASET Vitech Asia' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font Inter biar profesional --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full">

    <div>
        {{-- Include Sidebar --}}
        @include('partials.sidebar')

        {{-- Mobile Menu (Hidden by default) --}}
        <div class="mobile-menu hidden md:hidden fixed inset-0 z-40 flex">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="document.querySelector('.mobile-menu').classList.add('hidden')"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" onclick="document.querySelector('.mobile-menu').classList.add('hidden')">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                {{-- Copy isi sidebar menu buat mobile (Sederhana) --}}
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <span class="text-white font-bold text-xl">SIMASET</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="/" class="text-white group flex items-center px-2 py-2 text-base font-medium rounded-md bg-gray-900">Dashboard</a>
                        @if(auth()->user()->role == 'admin')
                            <a href="/assets" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-base font-medium rounded-md">Data Aset</a>
                            <a href="/users" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-base font-medium rounded-md">Manajemen User</a>
                        @else
                            <a href="/my-assets" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-base font-medium rounded-md">Aset Saya</a>
                            <a href="/assets" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-base font-medium rounded-md">Pinjam Aset</a>
                        @endif
                    </nav>
                </div>
            </div>
        </div>

        {{-- Main Content Wrapper --}}
        <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
            
            {{-- Include Topbar --}}
            @include('partials.topbar')

            {{-- Dynamic Content --}}
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        {{-- Flash Message Success/Error Global --}}
                        @if(session()->has('success'))
                            <div class="mb-4 rounded-md bg-green-50 p-4 border-l-4 border-green-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session()->has('loginError'))
                            <div class="mb-4 rounded-md bg-red-50 p-4 border-l-4 border-red-400">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">{{ session('loginError') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('container')
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>