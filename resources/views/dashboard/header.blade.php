<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <h2 class="text-3xl font-bold leading-tight text-gray-900">
            {{ $title }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Selamat datang, <span class="font-semibold text-indigo-600">{{ auth()->user()->name }}</span>.
        </p>
    </div>
    
    {{-- Area Tanggal & Jam Digital DIHAPUS (Pindah ke Topbar) --}}
</div>