@extends('layouts.main')

@section('container')
<div class="px-6 py-6 w-full font-sans text-slate-800">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Verifikasi Pengembalian Aset</h1>
            <p class="text-slate-500 mt-1 text-sm">Validasi kondisi aset yang dikembalikan oleh karyawan sebelum masuk stok.</p>
        </div>
        
        <div class="flex gap-3">
             {{-- Stats Card Mini --}}
             <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-xl flex items-center gap-3">
                <div class="bg-amber-100 p-1.5 rounded-lg text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Perlu Cek</p>
                    <p class="text-lg font-bold text-amber-700 leading-none">{{ $returns->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <div>
                <p class="font-bold">Berhasil</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Aset Dikembalikan</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi (User)</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $index => $return)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-slate-500">
                            {{ $returns->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                    {{ substr($return->user->name, 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $return->user->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $return->user->email }}</p>
                                </div>
                            </div>
                        </td>
                         <td class="px-4 py-3">
                            <div>
                                <p class="text-sm font-bold text-indigo-900">{{ $return->asset->name }}</p>
                                <p class="text-xs font-mono text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5">{{ $return->asset->serial_number }}</p>
                            </div>
                        </td>
                         <td class="px-4 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</span>
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($return->return_date)->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($return->condition == 'good')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Baik
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800 border border-rose-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Rusak
                                </span>
                            @endif
                            
                            @if($return->notes)
                                <div class="mt-1 text-xs text-slate-500 italic truncate max-w-[150px]" title="{{ $return->notes }}">
                                    "{{ Str::limit($return->notes, 20) }}"
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                             @if($return->status == 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
                                    Pending
                                </span>
                             @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Selesai
                                </span>
                             @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($return->status == 'pending')
                                <button onclick="openVerifyModal('{{ $return->id }}', '{{ $return->asset->name }}', '{{ $return->user->name }}', '{{ $return->condition }}', '{{ $return->photo_proof_1 }}', '{{ $return->photo_proof_2 }}', '{{ $return->photo_proof_3 }}')" 
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm shadow-indigo-500/20">
                                    Verifikasi
                                </button>
                            @else
                                <span class="text-xs font-medium text-slate-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                </div>
                                <p class="font-medium text-lg text-slate-500">Belum ada pengembalian</p>
                                <p class="text-sm">Data pengembalian aset akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($returns->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $returns->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL VERIFIKASI --}}
<div id="verifyModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeVerifyModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form id="verifyForm" method="POST" action="">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Verifikasi Pengembalian</h3>
                            <div class="mt-2 text-sm text-slate-500">
                                <p>Anda akan memverifikasi pengembalian aset <strong id="modalAssetName" class="text-slate-800"></strong> dari <strong id="modalUserName" class="text-slate-800"></strong>.</p>
                            </div>

                            {{-- [NEW] Tampilan Foto Bukti (Up to 3 Photos) --}}
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Bukti Foto User</label>
                                <div id="photoGrid" class="grid grid-cols-3 gap-2">
                                    {{-- Photos will be injected here via JS --}}
                                </div>
                                <p id="noPhotoText" class="text-xs text-slate-400 mt-2 hidden">Tidak ada foto disertakan</p>
                            </div>

                            <div class="mt-4 space-y-4">
                                <label class="block text-sm font-bold text-slate-700">Konfirmasi Kondisi Fisik</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="final_condition" value="available" class="peer sr-only" required>
                                        <div class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition">
                                            Layak (Stok +1)
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="final_condition" value="maintenance" class="peer sr-only">
                                        <div class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 transition">
                                            Perlu Servis
                                        </div>
                                    </label>
                                    <label class="relative flex cursor-pointer">
                                        <input type="radio" name="final_condition" value="broken" class="peer sr-only">
                                        <div class="w-full px-3 py-2 rounded-lg border-2 border-slate-200 text-slate-600 text-center text-xs font-bold peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition">
                                            Rusak Total
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- [NEW] Input Denda --}}
                            <div class="mt-4">
                                <label class="block text-sm font-bold text-slate-700 mb-1">Denda Kerusakan (Opsional)</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                      <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="fine" id="price" class="block w-full rounded-md border-0 py-1.5 pl-10 pr-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0">
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Isi jika kondisi rusak dan user dikenakan denda.</p>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Verifikasi & Terima
                    </button>
                    <button type="button" onclick="closeVerifyModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openVerifyModal(id, assetName, userName, userCondition, photo1, photo2, photo3) {
        const modal = document.getElementById('verifyModal');
        const form = document.getElementById('verifyForm');
        
        document.getElementById('modalAssetName').innerText = assetName;
        document.getElementById('modalUserName').innerText = userName;
        
        // [NEW] Show Photos
        const photoGrid = document.getElementById('photoGrid');
        const noPhotoText = document.getElementById('noPhotoText');
        
        // Reset
        photoGrid.innerHTML = '';
        
        const photos = [photo1, photo2, photo3].filter(p => p && p !== 'null' && p !== '');
        
        if (photos.length > 0) {
            noPhotoText.classList.add('hidden');
            photos.forEach(path => {
                const div = document.createElement('div');
                div.className = 'relative w-full h-24 bg-slate-100 rounded-lg overflow-hidden border border-slate-200 cursor-pointer group';
                div.innerHTML = `
                    <img src="/storage/${path}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" onclick="window.open(this.src, '_blank')">
                `;
                photoGrid.appendChild(div);
            });
        } else {
            noPhotoText.classList.remove('hidden');
        }

        // Auto-select radio button based on user report for convenience
        const conditionMap = {
            'good': 'available',
            'maintenance': 'maintenance',
            'broken': 'broken'
        };
        const mappedCondition = conditionMap[userCondition] || 'available';
        
        // Reset and check
        document.querySelectorAll('input[name="final_condition"]').forEach(el => el.checked = false);
        const radioToCkech = document.querySelector(`input[name="final_condition"][value="${mappedCondition}"]`);
        if(radioToCkech) radioToCkech.checked = true;

        form.action = `/returns/${id}/verify`;
        modal.classList.remove('hidden');
    }

    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('hidden');
    }
</script>
@endsection
