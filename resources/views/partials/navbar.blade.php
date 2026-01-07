<nav class="bg-gray-800" x-data="{ isOpen: false }">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 items-center justify-between">
      <div class="flex items-center">
        <div class="flex-shrink-0">
  <img class="h-10 w-10  object-cover border-2 border-white" 
       src="{{ asset('img/logoVitechAsia.png') }}" 
       alt="Foto Profil">
</div>
        <div class="hidden md:block">
          <div class="ml-50 flex items-baseline">
            <a href="/" class="{{ request()->is('/') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Home</a>
            <a href="/about" class="{{ request()->is('about') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">About</a>
            <a href="/blog" class="{{ request()->is('blog*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Blog</a>
            <a href="/contact" class="{{ request()->is('contact') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium">Contact</a>
            </div>
        </div>
      </div>
      
      <div class="hidden md:block">
        <div class="ml-4 flex items-center md:ml-6">
          @auth
            <div class="flex items-center gap-3">
                <span class="text-gray-300 text-sm">Halo, {{ auth()->user()->name }}</span>
                
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium">
                        Logout
                    </button>
                </form>
            </div>
          @else
            <a href="/login" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Login
            </a>
          @endauth
        </div>
      </div>

      <div class="-mr-2 flex md:hidden">
        <button type="button" @click="isOpen = !isOpen" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none">
          <span class="sr-only">Open main menu</span>
          <svg :class="{'hidden': isOpen, 'block': !isOpen }" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
          </svg>
          <svg :class="{'block': isOpen, 'hidden': !isOpen }" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <div x-show="isOpen" class="md:hidden">
    <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
      <a href="/" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium">Home</a>
      <a href="/about" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">About</a>
      <a href="/blog" class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium">Blog</a>
      
      <div class="border-t border-gray-700 pt-4 pb-3">
        @auth
            <div class="flex items-center px-5 mb-3">
                <div class="text-base font-medium leading-none text-white">{{ auth()->user()->name }}</div>
                <div class="text-sm font-medium leading-none text-gray-400 ml-2">{{ auth()->user()->email }}</div>
            </div>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Sign out</button>
            </form>
        @else
            <a href="/login" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Login</a>
        @endauth
      </div>

    </div>
  </div>
</nav>