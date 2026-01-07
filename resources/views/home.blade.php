@extends('layouts.main')

@section('container')
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Dashboard Aset IT
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                PT Vitech Asia - Asset Management System
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="/assets/create" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                </svg>
                Input Aset Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        
        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6 border-l-4 border-indigo-500">
            <dt>
                <div class="absolute rounded-md bg-indigo-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Inventaris</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                <p class="text-2xl font-semibold text-gray-900">{{ $totalAssets }}</p>
                <p class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                    <span class="sr-only">Increased by</span>
                    Unit
                </p>
            </dd>
        </div>

        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6 border-l-4 border-green-500">
            <dt>
                <div class="absolute rounded-md bg-green-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Tersedia (Ready)</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                <p class="text-2xl font-semibold text-gray-900">{{ $availableAssets }}</p>
            </dd>
        </div>

        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6 border-l-4 border-blue-500">
            <dt>
                <div class="absolute rounded-md bg-blue-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Digunakan Karyawan</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                <p class="text-2xl font-semibold text-gray-900">{{ $deployedAssets }}</p>
            </dd>
        </div>

        <div class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6 border-l-4 border-red-500">
            <dt>
                <div class="absolute rounded-md bg-red-500 p-3">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.794 1.871c-1.472-1.472-3.818-1.576-5.063-.145-1.298 1.492.38 5.768 2.302 4.414 1.258-.885 1.705-3.21.674-4.269z" />
                    </svg>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Maintenance / Rusak</p>
            </dt>
            <dd class="ml-16 flex items-baseline pb-1 sm:pb-2">
                <p class="text-2xl font-semibold text-gray-900">{{ $maintenanceAssets }}</p>
            </dd>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Aset Terbaru</h3>
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Real-time Update</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemegang (User)</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Beli</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($recentAssets as $asset)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <img class="h-10 w-10 rounded-lg object-cover bg-gray-100" src="https://ui-avatars.com/api/?name={{ urlencode($asset->name) }}&background=random&color=fff" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($asset->description, 25) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded border">{{ $asset->serial_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asset->status == 'available')
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Available</span>
                            @elseif($asset->status == 'deployed')
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Deployed</span>
                            @elseif($asset->status == 'maintenance')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Maintenance</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Broken</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($asset->holder)
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600">
                                        {{ substr($asset->holder->name, 0, 1) }}
                                    </div>
                                    {{ $asset->holder->name }}
                                </div>
                            @else
                                <span class="text-gray-400 italic text-xs">- Di Gudang -</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ $asset->purchase_date->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada aset</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan aset baru.</p>
                            <div class="mt-6">
                                <a href="/assets/create" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                                    </svg>
                                    Tambah Aset
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
            <p class="text-xs text-gray-500">Menampilkan 5 data terbaru</p>
            <a href="/assets" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">Lihat Semua Aset &rarr;</a>
        </div>
    </div>
@endsection