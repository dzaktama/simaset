@extends('layouts.main')

@section('container')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 bg-gray-50 min-h-screen">
    {{-- Header Section --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Data Pengguna</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi profil dan hak akses pengguna.</p>
        </div>
        <a href="/users" class="group inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-700 shadow-sm border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
            <svg class="w-4 h-4 mr-2 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @php
        $userPerms = $user->permissions->pluck('slug')->toArray() ?? [];
    @endphp

    <form action="/users/{{ $user->id }}" method="POST" @submit="confirmSave($event)" x-data="{
        currentRole: '{{ old('role', $user->role?->slug) }}',
        isCustomMode: false, // Mode Kustom untuk edit manual
        showSidebarPreview: false, // Toggle Sidebar Preview
        mandatory: {
            // Super Admin - SATU-SATUNYA yang wajib punya user management
            'super_admin': ['dashboard.view', 'dashboard.stats', 'asset.view', 'asset.create', 'asset.edit', 'asset.delete', 'asset.export', 'borrow.action', 'return.verify', 'report.view', 'maintenance.view', 'chat.access', 'user.view', 'user.create', 'user.edit', 'user.delete'],
            // Admin - TANPA user management (user.*)
            'admin': ['dashboard.view', 'dashboard.stats', 'asset.view', 'asset.create', 'asset.edit', 'asset.delete', 'asset.export', 'borrow.action', 'return.verify', 'report.view', 'maintenance.view', 'chat.access'],
            // Teknisi - Fokus maintenance & verifikasi
            'service_center': ['dashboard.view', 'dashboard.stats', 'asset.view', 'asset.edit', 'maintenance.view', 'maintenance.create', 'maintenance.action', 'return.verify', 'chat.access', 'borrow.request'],
            'tek': ['dashboard.view', 'dashboard.stats', 'asset.view', 'asset.edit', 'maintenance.view', 'maintenance.create', 'maintenance.action', 'return.verify', 'chat.access', 'borrow.request'],
            // Staff - Hanya akses dasar (view, request, lapor)
            'user': ['dashboard.view', 'asset.view', 'borrow.view', 'borrow.request', 'maintenance.create', 'chat.access'],
            'staff': ['dashboard.view', 'asset.view', 'borrow.view', 'borrow.request', 'maintenance.create', 'chat.access']
        },
        selectedPermissions: {{ json_encode($userPerms) }}, // Sinkronisasi array state checkbox
        checkAll(group) { 
            // Ambil semua value permission dari group ini
            let els = document.querySelectorAll('.' + group);
            els.forEach(el => {
                if(!this.selectedPermissions.includes(el.value)) {
                    this.selectedPermissions.push(el.value);
                }
            });
        },
        uncheckAll(group) { 
            let els = document.querySelectorAll('.' + group);
            els.forEach(el => {
                // Hanya hapus jika tidak terkunci
                if(!this.isLocked(el.value)) {
                     this.selectedPermissions = this.selectedPermissions.filter(p => p !== el.value);
                }
            }); 
        },
        // Cek apakah permission terkunci (wajib ada untuk role tersebut)
        isLocked(perm) {
            if (this.isCustomMode) return false; // Jika mode kustom aktif, matikan gembok
            if (!this.currentRole) return false;
            let list = this.mandatory[this.currentRole] || [];
            return list.includes(perm);
        },
        checkMandatory() {
            if (this.isCustomMode) return; // Skip jika mode kustom
            let list = this.mandatory[this.currentRole] || [];
            list.forEach(perm => {
                 if(!this.selectedPermissions.includes(perm)) {
                     this.selectedPermissions.push(perm);
                 }
            });
            // Update Duplicate Input Manually via x-model reactivity is automatic
        },
        updateRole(role, event) {
            // Reset ke mode normal jika ganti role
            this.isCustomMode = false;
            
            if(confirm('Ubah Role? Permission akan di-reset sesuai role baru.')) {
                this.currentRole = role;
                this.applyPreset(role);
            } else {
                event.target.value = this.currentRole;
            }
        },
        applyPreset(role) {
            // Reset semua checkbox (kosongkan array)
            this.selectedPermissions = [];
            
            // =====================================================
            // SUPER ADMIN - AKSES PENUH KE SEMUA FITUR
            // =====================================================
            if (role === 'super_admin') { 
                // Kita push manual array permisionnya
                // Legacy groups (Default view) - ALL
                // Note: checkAll helper skrg manipulasi array selectedPermissions
                this.checkAll('perm-asset');
                this.checkAll('perm-borrow');
                this.checkAll('perm-maint');
                this.checkAll('perm-report');
                this.checkAll('perm-user'); 
                this.checkAll('perm-others');
            }
            // =====================================================
            // ADMINISTRATOR - SEMUA KECUALI MANAJEMEN USER
            // =====================================================
            else if (role === 'admin') { 
                this.checkAll('perm-asset');
                this.checkAll('perm-borrow');
                this.checkAll('perm-maint');
                this.checkAll('perm-report');
                this.checkAll('perm-others');
                // TIDAK checkAll('perm-user')
            }
            // =====================================================
            // TEKNISI - FOKUS MAINTENANCE & SERVIS
            // =====================================================
            else if (role === 'service_center' || role === 'tek') { 
                // Akses dasar & Maintenance
                let permissions = [
                    'dashboard.view', 'dashboard.stats', 'asset.view', 'asset.map', 'chat.access',
                    'maintenance.view', 'maintenance.create', 'maintenance.action',
                    'borrow.view', 'borrow.request', 'return.verify',
                    'asset.edit' // Mutasi Aset
                ];
                this.selectedPermissions.push(...permissions);
            }
            // =====================================================
            // STAFF - END USER BASIC (PALING TERBATAS)
            // =====================================================
            else if (role === 'user' || role === 'staff') { 
                let permissions = [
                    'dashboard.view', 'asset.view', 'chat.access',
                    'borrow.request', 'borrow.view', 'maintenance.create'
                ];
                this.selectedPermissions.push(...permissions);
            }
            
            this.checkMandatory();
        },
        // Helper untuk set checked semua instances
        setChecked(val) {
             if(!this.selectedPermissions.includes(val)) {
                this.selectedPermissions.push(val);
             }
        },
        // Konfirmasi ganda sebelum simpan (Request User)
        confirmSave(e) {
            e.preventDefault();
            
            // Konfirmasi Pertama
            if(!confirm('Apakah Anda yakin ingin menyimpan perubahan data user ini?')) return;
            
            // Konfirmasi Kedua (Keamanan)
            if(!confirm('Konfirmasi Keamanan: Perubahan hak akses dapat mempengaruhi keamanan sistem. Anda yakin data sudah benar?')) return;
            
            e.target.submit();
        },
        employeeIdPreview: '{{ $user->employee_id }}',
        init() {
            // DETEKSI OTOMATIS: Jika ada mandatory permission yang TIDAK ter-centang (dari DB),
            // maka otomatis aktifkan Custom Mode agar tidak dipaksa checklist kembali.
            if (this.currentRole && this.mandatory[this.currentRole]) {
                let requiredPerms = this.mandatory[this.currentRole];
                // Cek array selectedPermissions apakah ada yg kurang
                let isMissingMandatory = requiredPerms.some(perm => !this.selectedPermissions.includes(perm));

                if (isMissingMandatory) {
                    this.isCustomMode = true;
                } else {
                    // Jika semua lengkap/standar, barulah kita enforce (jaga-jaga)
                    this.checkMandatory();
                }
            } else {
                 this.checkMandatory();
            }
        }
    }">
        @method('put')
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- BAGIAN KIRI: Form Input / Preview Sidebar (4 Kolom) --}}
            <div class="lg:col-span-4 space-y-5">
                
                {{-- HEADER & TOGGLE SWITCH --}}
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Preview</h3>
                    
                    {{-- Toggle Button --}}
                    <div class="flex items-center bg-gray-200 rounded-lg p-1 relative">
                        <div class="w-24 h-7 bg-white rounded shadow-sm absolute transition-all duration-300 ease-out"
                             :class="showSidebarPreview ? 'translate-x-24' : 'translate-x-0'"></div>
                        
                        <button type="button" @click="showSidebarPreview = false"
                                class="relative z-10 w-24 py-1 text-[10px] font-bold uppercase tracking-wider transition-colors duration-200"
                                :class="!showSidebarPreview ? 'text-indigo-700' : 'text-gray-500 hover:text-gray-700'">
                            Form Data
                        </button>
                        <button type="button" @click="showSidebarPreview = true"
                                class="relative z-10 w-24 py-1 text-[10px] font-bold uppercase tracking-wider transition-colors duration-200"
                                :class="showSidebarPreview ? 'text-indigo-700' : 'text-gray-500 hover:text-gray-700'">
                            Sidebar
                        </button>
                    </div>
                </div>

                {{-- MODE FORM INPUT --}}
                <div x-show="!showSidebarPreview" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="space-y-5">
                    
                {{-- Card 1: Informasi Akun --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100 flex items-center">
                        <div class="bg-indigo-100 p-1.5 rounded-md mr-3 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <h3 class="text-base font-bold text-indigo-900">Informasi Akun</h3>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $user->name) }}" 
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium" 
                                placeholder="Masukkan nama lengkap">
                            @error('name') <p class="text-red-600 text-xs mt-1 font-semibold flex items-center"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Email Kantor <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">@</span>
                                <input type="email" name="email" required value="{{ old('email', $user->email) }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 pl-8 pr-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium" 
                                    placeholder="nama@perusahaan.com">
                            </div>
                            @error('email') <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5 group-focus-within:text-indigo-600">Password Baru <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="password" name="password" 
                                class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all duration-200 font-medium" 
                                placeholder="Isi jika ingin ganti password">
                            @error('password') <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Role Pengguna <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="role" @change="updateRole($event.target.value, $event)" class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 font-medium focus:border-indigo-600 focus:bg-white focus:ring-0 appearance-none cursor-pointer transition-all">
                                    <option value="user" {{ old('role', $user->role?->slug) == 'user' ? 'selected' : '' }}>Karyawan (User)</option>
                                    <option value="admin" {{ old('role', $user->role?->slug) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="service_center" {{ old('role', $user->role?->slug) == 'service_center' ? 'selected' : '' }}>Teknisi (Service Center)</option>
                                    @if(auth()->user()->role?->slug === 'super_admin')
                                        <option value="super_admin" {{ old('role', $user->role?->slug) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                    @endif
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Detail Pekerjaan --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center">
                        <div class="bg-white p-1 rounded-md border border-gray-200 shadow-sm mr-3 text-gray-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Detail Pekerjaan</h3>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        <div class="group" x-data="{ employeeId: '{{ old('employee_id', $user->employee_id) }}' }">
                            <label class="block text-xs uppercase tracking-wider font-bold text-gray-500 mb-1">Employee ID</label>
                            <div class="flex gap-2">
                                <input type="text" name="employee_id" x-model="employeeId" required 
                                    class="w-full rounded-lg border border-gray-300 bg-gray-200 px-3 py-2.5 text-sm text-gray-700 font-bold cursor-not-allowed focus:ring-0 focus:border-gray-300" 
                                    readonly
                                    placeholder="ID Karyawan">
                                
                                @if(empty($user->employee_id))
                                <button type="button" @click="employeeId = '{{ $suggestedId }}'" 
                                    class="whitespace-nowrap px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-2 transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Auto Generate
                                </button>
                                @endif
                            </div>
                            <div class="mt-1.5 text-center text-[10px] text-gray-500">
                                @if(empty($user->employee_id))
                                    <span class="text-amber-600 font-semibold">⚠ User ini belum memiliki ID. Klik tombol Auto Generate.</span>
                                @else
                                    ID Karyawan bersifat unik dan tidak disarankan diubah.
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Departemen</label>
                                <input type="text" name="department" required value="{{ old('department', $user->department) }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all font-medium" 
                                    placeholder="IT/HR">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Jabatan</label>
                                <input type="text" name="position" required value="{{ old('position', $user->position) }}" 
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all font-medium" 
                                    placeholder="Manager">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Nomor WhatsApp</label>
                            <div class="flex">
                                <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 px-3 text-gray-600 font-bold text-sm">+62</span>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                                    class="w-full rounded-r-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-600 focus:bg-white focus:ring-0 transition-all font-medium" 
                                    placeholder="812-3456-7890">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                {{-- MODE PREVIEW SIDEBAR --}}
                <div x-show="showSidebarPreview" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                    @include('users.partials.sidebar-preview')
                </div>

            </div>

            {{-- BAGIAN KANAN: Permissions (8 Kolom) --}}
            <div class="lg:col-span-8">
                @if(auth()->user()->role?->slug === 'super_admin')
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden h-full flex flex-col" x-data="{ viewMode: 'legacy' }">
                    {{-- Header Panel Putih (Light Theme) --}}
                    <div class="p-5 md:p-6 bg-white border-b border-gray-200 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-bold flex items-center gap-2 text-gray-800">
                                <div class="p-1.5 bg-indigo-50 rounded-md">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                </div>
                                Izin Kontrol Hak Akses
                            </h3>
                            <div class="flex items-center gap-4 mt-1 ml-9">
                                {{-- Toggle Custom Mode --}}
                                <label class="inline-flex items-center cursor-pointer group">
                                    <input type="checkbox" x-model="isCustomMode" class="sr-only peer">
                                    <div class="relative w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ms-2 text-xs font-bold text-gray-500 group-hover:text-gray-700 transition-colors flex items-center gap-1">
                                        <template x-if="isCustomMode">
                                            <span class="flex items-center gap-1 text-indigo-600">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                                Mode Custom
                                            </span>
                                        </template>
                                        <template x-if="!isCustomMode">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                                Terkunci
                                            </span>
                                        </template>
                                    </span>
                                </label>

                                {{-- Toggle View Mode (Legacy vs Section) --}}
                                <div class="flex items-center bg-gray-100 border border-gray-200 rounded-lg p-0.5"> 
                                    <button type="button" @click="viewMode = 'legacy'" :class="viewMode === 'legacy' ? 'bg-white text-gray-800 shadow-sm border border-gray-200' : 'text-gray-400 hover:text-gray-600'" class="px-2 py-1 rounded-md text-[10px] font-bold transition-all flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                        DEFAULT
                                    </button>
                                    <button type="button" @click="viewMode = 'sections'" :class="viewMode === 'sections' ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100' : 'text-gray-400 hover:text-gray-600'" class="px-2 py-1 rounded-md text-[10px] font-bold transition-all flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                        SECTIONS
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 self-center mr-1">Preset:</span>
                            <button type="button" @click="applyPreset('super_admin')" class="px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-amber-500 text-white hover:bg-amber-600 transition shadow-lg shadow-amber-500/30">SUPER ADMIN</button>
                            <button type="button" @click="applyPreset('admin')" class="px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-indigo-600 text-white hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/30">ADMINISTRATOR</button>
                            <button type="button" @click="applyPreset('tek')" class="px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-emerald-600 text-white hover:bg-emerald-500 transition shadow-lg shadow-emerald-500/30">TEKNISI</button>
                            <button type="button" @click="applyPreset('staff')" class="px-2.5 py-1.5 rounded-md text-[10px] font-bold bg-gray-600 text-white hover:bg-gray-500 transition shadow-lg shadow-gray-500/30">STAFF</button>
                        </div>
                    </div>
                    
                    <div class="p-5 md:p-6 flex-grow bg-gray-50">
                        @php
                            // $userPerms moved to top
                            
                            // 1. DEFAULT VIEW - Per Jenis Fitur (6 Groups)
                            $legacyGroups = [
                                [
                                    'id' => 'perm-asset',
                                    'title' => 'Manajemen Aset',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
                                    'items' => ['dashboard.stats'=>'Dashboard Gudang', 'asset.view'=>'Katalog Aset', 'asset.create'=>'Input Aset Baru', 'asset.edit'=>'Mutasi Aset', 'asset.delete'=>'Hapus Aset', 'asset.export'=>'Export Excel', 'asset.map'=>'Lokasi Barang']
                                ],
                                [
                                    'id' => 'perm-borrow',
                                    'title' => 'Sirkulasi / Peminjaman',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                                    'items' => ['borrow.view'=>'Riwayat Peminjaman', 'borrow.request'=>'Aset Saya', 'borrow.action'=>'Approval Peminjaman', 'return.verify'=>'Verifikasi Pengembalian']
                                ],
                                [
                                    'id' => 'perm-maint',
                                    'title' => 'Maintenance & Servis',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                                    'items' => ['maintenance.view'=>'Jadwal Servis', 'maintenance.create'=>'Lapor Kerusakan', 'maintenance.action'=>'Perbaikan Barang']
                                ],
                                [
                                    'id' => 'perm-report',
                                    'title' => 'Laporan & Statistik',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                                    'items' => ['report.view'=>'Akses Laporan', 'report.export'=>'Download PDF']
                                ],
                                [
                                    'id' => 'perm-user',
                                    'title' => 'Manajemen User',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                                    'items' => ['user.view'=>'Lihat Daftar', 'user.create'=>'Tambah User', 'user.edit'=>'Edit User', 'user.delete'=>'Hapus User']
                                ],
                                [
                                    'id' => 'perm-others',
                                    'title' => 'Lainnya',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />',
                                    'items' => ['chat.access'=>'Pesan & Diskusi', 'dashboard.view'=>'Dashboard']
                                ]
                            ];

                            // 2. SECTIONS VIEW - Per Section Sidebar (4 Groups)
                            $sectionGroups = [
                                [
                                    'id' => 'sec-master',
                                    'title' => 'MASTER',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                                    'items' => ['dashboard.view'=>'Dashboard', 'dashboard.stats'=>'Dashboard Gudang', 'asset.view'=>'Katalog Aset', 'asset.map'=>'Lokasi Barang', 'asset.delete'=>'Hapus Aset', 'asset.export'=>'Export Excel']
                                ],
                                [
                                    'id' => 'sec-trans',
                                    'title' => 'TRANSAKSI',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />',
                                    'items' => ['chat.access'=>'Pesan & Diskusi', 'asset.create'=>'Input Aset Baru', 'maintenance.create'=>'Lapor Kerusakan', 'borrow.action'=>'Approval Peminjaman', 'return.verify'=>'Verifikasi Pengembalian', 'asset.edit'=>'Mutasi Aset', 'maintenance.action'=>'Perbaikan Barang', 'maintenance.view'=>'Jadwal Servis']
                                ],
                                [
                                    'id' => 'sec-report',
                                    'title' => 'LAPORAN',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                                    'items' => ['report.view'=>'Laporan & Audit', 'report.export'=>'Download PDF', 'borrow.view'=>'Riwayat Peminjaman']
                                ],
                                [
                                    'id' => 'sec-util',
                                    'title' => 'UTILITAS',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                                    'items' => ['borrow.request'=>'Aset Saya', 'user.view'=>'Manajemen User', 'user.create'=>'Tambah User', 'user.edit'=>'Edit User', 'user.delete'=>'Hapus User']
                                ]
                            ];
                        @endphp

                        {{-- VIEW MODE: DEFAULT - Per Jenis Fitur --}}
                        <div x-show="viewMode === 'legacy'" class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all duration-300">
                            @foreach($legacyGroups as $group)
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100">
                                    <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $group['icon'] !!}</svg>
                                        {{ $group['title'] }}
                                    </h4>
                                    <div class="flex gap-1.5 text-[10px] font-bold">
                                        <button type="button" @click="checkAll('{{ $group['id'] }}')" class="px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition">ALL</button>
                                        <button type="button" @click="uncheckAll('{{ $group['id'] }}')" class="px-2 py-1 text-red-400 hover:text-red-500 hover:bg-red-50 rounded transition">CLR</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($group['items'] as $val => $label)
                                        @php $isChecked = in_array($val, $userPerms) ? 'checked' : ''; @endphp
                                    <label class="relative flex items-center justify-between p-2.5 rounded-lg border border-gray-200 bg-white hover:border-indigo-400 hover:bg-gray-50 transition-all cursor-pointer group">
                                        <span class="text-xs font-medium text-gray-700 group-hover:text-gray-900">{{ $label }}</span>
                                        <input type="checkbox" name="permissions[]" value="{{ $val }}" x-model="selectedPermissions"
                                            @click="if(isLocked('{{ $val }}')) $event.preventDefault()"
                                            class="{{ $group['id'] }} w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer"
                                            :class="{ 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': isLocked('{{ $val }}') }">
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- VIEW MODE: SECTIONS - Per Section Sidebar --}}
                        <div x-show="viewMode === 'sections'" class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all duration-300" style="display: none;">
                            @foreach($sectionGroups as $group)
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100">
                                    <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $group['icon'] !!}</svg>
                                        {{ $group['title'] }}
                                    </h4>
                                    <div class="flex gap-1.5 text-[10px] font-bold">
                                        <button type="button" @click="checkAll('{{ $group['id'] }}')" class="px-2 py-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition">ALL</button>
                                        <button type="button" @click="uncheckAll('{{ $group['id'] }}')" class="px-2 py-1 text-red-400 hover:text-red-500 hover:bg-red-50 rounded transition">CLR</button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($group['items'] as $val => $label)
                                        @php $isChecked = in_array($val, $userPerms) ? 'checked' : ''; @endphp
                                    <label class="relative flex items-center justify-between p-2.5 rounded-lg border border-gray-200 bg-white hover:border-indigo-400 hover:bg-gray-50 transition-all cursor-pointer group">
                                        <span class="text-xs font-medium text-gray-700 group-hover:text-gray-900">{{ $label }}</span>
                                        <input type="checkbox" name="permissions[]" value="{{ $val }}" x-model="selectedPermissions"
                                            @click="if(isLocked('{{ $val }}')) $event.preventDefault()"
                                            class="{{ $group['id'] }} w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer"
                                            :class="{ 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': isLocked('{{ $val }}') }">
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <button type="submit" class="w-full flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-indigo-500/30 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </form>
</div>
@endsection