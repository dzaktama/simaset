<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
            <tr>
                <th scope="col" class="px-6 py-4">Info Aset</th>
                <th scope="col" class="px-6 py-4">Kategori & Lokasi</th>
                <th scope="col" class="px-6 py-4">Status & Stok</th>
                <th scope="col" class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @php
                // Group assets by name
                $groupedAssets = $assets->groupBy('name');
            @endphp

            @forelse ($groupedAssets as $name => $group)
                @if ($group->count() > 1)
                    {{-- PARENT ROW (GROUP) --}}
                    <tbody x-data="{ expanded: false }" class="border-b border-gray-100 last:border-0">
                        <tr class="bg-white hover:bg-gray-50 transition cursor-pointer" @click="expanded = !expanded">
                            {{-- Kolom 1: Info Grup --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative">
                                        @if($group->first()->image && $group->first()->image !== 'null')
                                            <img class="h-full w-full object-cover" src="/storage/{{ $group->first()->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 right-0 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded-tl font-bold">{{ $group->count() }} Unit</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $name }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-1">
                                            {{ $group->count() > 1 ? $group->count() . ' Unit (Berbagai SN)' : $group->first()->serial_number }}
                                        </div>
                                        <div class="text-xs text-indigo-600 font-medium flex items-center gap-1 mt-1">
                                            <span x-text="expanded ? 'Tutup Daftar' : 'Lihat ' + {{ $group->count() }} + ' Unit'"></span>
                                            <svg x-show="!expanded" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            <svg x-show="expanded" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Kolom 2: Kategori (Parent) --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $group->first()->category ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-1">Berbagai Lokasi</div>
                            </td>

                            {{-- Kolom 3: Summary Status --}}
                            <td class="px-6 py-4">
                                <div class="flex gap-1 flex-wrap">
                                    @php
                                        $statusCounts = $group->countBy('status');
                                    @endphp
                                    @foreach($statusCounts as $status => $count)
                                        @php
                                            $colors = [
                                                'available' => 'bg-green-100 text-green-800 border-green-200',
                                                'deployed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'broken' => 'bg-red-100 text-red-800 border-red-200',
                                                'lost' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            ];
                                            $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                            {{ $count }} {{ ucfirst($status) }}
                                        </span>
                                    @endforeach
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1 group/stock">
                                        Total {{ $group->sum('quantity') }}
                                        @if(in_array(auth()->user()->role?->slug, ['admin', 'super_admin']))
                                        <button type="button" 
                                                onclick="event.stopPropagation(); openAddStockModal({{ $group->first()->makeHidden(['image', 'image2', 'image3']) }})" 
                                                class="ml-1 p-0.5 rounded hover:bg-indigo-200 text-indigo-500 hover:text-indigo-800 transition" 
                                                title="Tambah Stok Aset Ini">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Kolom 4: Aksi Parent (Expand Toggle) --}}
                            <td class="px-6 py-4 text-center">
                                <button class="text-gray-400 hover:text-indigo-600 transition">
                                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        {{-- CHILD ROWS (Items) --}}
                        @foreach ($group as $asset)
                            <tr x-show="expanded" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="bg-gray-50/80 border-l-4 border-l-indigo-500 hover:bg-indigo-50/30 transition duration-150">
                                
                                {{-- Child Kolom 1: Image + Name + SN --}}
                                <td class="px-6 py-3 pl-12">
                                    <div class="flex items-center gap-4">
                                        {{-- Garis Hierarki Visual --}}
                                        <div class="absolute left-6 w-4 h-8 border-b-2 border-l-2 border-indigo-200 rounded-bl-xl -translate-y-4"></div>
                                        
                                        <div class="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative group z-10">
                                            @if($asset->image && $asset->image !== 'null')
                                                <img class="h-full w-full object-cover" src="/storage/{{ $asset->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-700">{{ $asset->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $asset->serial_number }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Child Kolom 2: Category + Lokasi --}}
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-900 mb-0.5">{{ $asset->category ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $asset->lorong ?? '-' }} - Rak {{ $asset->rak ?? '-' }}
                                    </div>
                                </td>

                                {{-- Child Kolom 3: Status Style --}}
                                <td class="px-6 py-3">
                                    @if ($asset->status === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Available</span>
                                    @elseif ($asset->status === 'deployed')
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Deployed</span>
                                            <span class="text-[10px] text-gray-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                {{ $asset->holder->name ?? '-' }}
                                            </span>
                                        </div>
                                    @elseif ($asset->status === 'maintenance')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">Maintenance</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Broken</span>
                                    @endif
                                </td>

                                {{-- Child Kolom 4: Actions --}}
                                <td class="px-6 py-3 text-center">
                                    @include('assets.partials.action-buttons', ['asset' => $asset])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                @else
                    {{-- SINGLE ITEM ROW (No Grouping Needed) --}}
                    @php $asset = $group->first(); @endphp
                    <tr class="bg-white hover:bg-gray-50 transition duration-150">
                        {{-- Kolom 1 --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 relative group">
                                    @if($asset->image && $asset->image !== 'null')
                                        <img class="h-full w-full object-cover" src="/storage/{{ $asset->image }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center text-gray-400\'><svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'></path></svg></div>';">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5 bg-gray-100 px-1 rounded w-fit">{{ $asset->serial_number }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom 2 --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">{{ $asset->category ?? '-' }}</div>
                            <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $asset->lorong ?? '-' }} - Rak {{ $asset->rak ?? '-' }}
                            </div>
                        </td>

                        {{-- Kolom 3 --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 mb-1">
                                @if ($asset->status === 'available')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Available</span>
                                @elseif ($asset->status === 'deployed')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Deployed</span>
                                @elseif ($asset->status === 'maintenance')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Maintenance</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Broken</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">Stok: <b>{{ $asset->quantity }}</b> Unit</div>
                        </td>

                        {{-- Kolom 4 --}}
                        <td class="px-6 py-4 text-center">
                            @include('assets.partials.action-buttons', ['asset' => $asset])
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center bg-gray-50 rounded-b-xl">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-500 font-medium">Belum ada data aset.</p>
                            <p class="text-xs text-gray-400 mt-1">Silakan tambahkan aset baru.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 text-right">
    Menampilkan semua data (Auto-scroll) • Total {{ $assets->count() }} Aset
</div>