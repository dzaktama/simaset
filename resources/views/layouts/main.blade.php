<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'] },
          }
        }
      }
    </script>

    <title>SIMASET - PT Vitech Asia</title>
</head>

<body class="h-full font-sans antialiased text-gray-900">

<div x-data="{ sidebarOpen: false }" class="min-h-full flex">

    <div x-show="sidebarOpen" class="relative z-50 md:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/80 transition-opacity" @click="sidebarOpen = false"></div>
        <div class="fixed inset-0 flex">
            <div class="relative mr-16 flex w-full max-w-xs flex-1">
                @include('partials.sidebar')
                
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-gray-50">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
        @include('partials.sidebar')
    </div>

    <div class="flex flex-1 flex-col md:pl-64 h-screen overflow-hidden">
        
        @include('partials.topbar')

        <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
            <div class="mx-auto max-w-7xl">
                @if(session()->has('success'))
                    <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('container')
            </div>
        </main>
    </div>

</div>

</body>
</html>