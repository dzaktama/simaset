@extends('layouts.main')

@section('container')
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-3xl font-bold leading-tight text-gray-900">Manajemen Pengguna</h2>
            <p class="mt-2 text-sm text-gray-600">Kelola akun karyawan, administrator, dan hak akses sistem.</p>
        </div>
        <a href="/users/create" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            Tambah User Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kontak & Info</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aset Dipegang</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $user->employee_id ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            <div class="text-xs text-gray-500">{{ $user->position ?? 'Staff' }} - {{ $user->department ?? 'General' }}</div>
                            @if($user->phone)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $user->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role == 'admin')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Administrator</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Karyawan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->assets->count() > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->assets->count() }} Unit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                {{-- TOMBOL DETAIL (MODAL) --}}
                                <button onclick="openUserModal({{ json_encode($user) }}, {{ json_encode($user->assets) }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded border border-indigo-200 hover:bg-indigo-100 transition">
                                    Detail
                                </button>

                                <a href="/users/{{ $user->id }}/edit" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 px-3 py-1 rounded border border-yellow-200 hover:bg-yellow-100 transition">
                                    Edit
                                </a>
                                
                                <form action="/users/{{ $user->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini? Aksi ini tidak bisa dibatalkan.')">
                                    @method('delete')
                                    @csrf
                                    <button class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded border border-red-200 hover:bg-red-100 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Belum ada data pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL DETAIL USER ================= --}}
    <div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeUserModal()"></div>

            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                {{-- Header --}}
                <div class="bg-indigo-600 px-4 py-4 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Profil Pengguna
                    </h3>
                    <button onclick="closeUserModal()" class="text-indigo-200 hover:text-white transition">&times;</button>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        
                        {{-- Kiri: Info Dasar --}}
                        <div class="w-full md:w-1/2 space-y-4 border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0 md:pr-4">
                            <div class="flex items-center gap-4">
                                <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-2xl" id="modalUserInitial">
                                    -
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900" id="modalUserName">-</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" id="modalUserRole">-</span>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm mt-4">
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">NIP / ID</span>
                                    <span class="font-medium text-gray-900 font-mono" id="modalUserNIP">-</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">Email</span>
                                    <span class="font-medium text-gray-900" id="modalUserEmail">-</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">No. HP</span>
                                    <span class="font-medium text-gray-900" id="modalUserPhone">-</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1">
                                    <span class="text-gray-500">Departemen</span>
                                    <span class="font-medium text-gray-900" id="modalUserDept">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Jabatan</span>
                                    <span class="font-medium text-gray-900" id="modalUserPos">-</span>
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Aset yg Dipegang --}}
                        <div class="w-full md:w-1/2">
                            <h4 class="text-sm font-bold text-gray-900 uppercase mb-3 flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Aset Sedang Dipinjam
                            </h4>
                            
                            <div id="modalUserAssetsList" class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                {{-- List injected by JS --}}
                            </div>
                            <div id="modalNoAssets" class="hidden text-center py-4 text-gray-500 italic text-sm bg-gray-50 rounded-lg">
                                Tidak ada aset yang sedang dipinjam.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end">
                    <button onclick="closeUserModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUserModal(user, assets) {
            // 1. Set Profil
            document.getElementById('modalUserName').innerText = user.name;
            document.getElementById('modalUserInitial').innerText = user.name.charAt(0).toUpperCase();
            document.getElementById('modalUserRole').innerText = user.role === 'admin' ? 'Administrator' : 'Karyawan';
            document.getElementById('modalUserNIP').innerText = user.employee_id || '-';
            document.getElementById('modalUserEmail').innerText = user.email;
            document.getElementById('modalUserPhone').innerText = user.phone || '-';
            document.getElementById('modalUserDept').innerText = user.department || '-';
            document.getElementById('modalUserPos').innerText = user.position || '-';

            // 2. Set List Aset
            const listContainer = document.getElementById('modalUserAssetsList');
            const noAssetsMsg = document.getElementById('modalNoAssets');
            
            listContainer.innerHTML = ''; // Reset list

            if (assets.length > 0) {
                noAssetsMsg.classList.add('hidden');
                assets.forEach(asset => {
                    const assignedDate = asset.assigned_date ? new Date(asset.assigned_date).toLocaleDateString('id-ID') : 'Baru saja';
                    
                    const itemHtml = `
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-blue-50 transition">
                            <div class="h-8 w-8 rounded bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900 truncate">${asset.name}</p>
                                <p class="text-xs text-gray-500 font-mono">${asset.serial_number}</p>
                                <p class="text-[10px] text-blue-600 mt-1">Sejak: ${assignedDate}</p>
                            </div>
                        </div>
                    `;
                    listContainer.insertAdjacentHTML('beforeend', itemHtml);
                });
            } else {
                noAssetsMsg.classList.remove('hidden');
            }

            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }
    </script>
@endsection