@extends('layouts.main')

@section('container')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h2>
            <p class="text-sm text-gray-500">Registrasi akun karyawan atau administrator baru.</p>
        </div>
        <a href="/users" class="rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <form action="/users" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- KOLOM KIRI: INFO AKUN --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Informasi Akun</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="Contoh: Budi Santoso">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="budi@kantor.com">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password Default <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="Minimal 5 karakter">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role / Hak Akses <span class="text-red-500">*</span></label>
                        <select name="role" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5">
                            <option value="user">Karyawan (User)</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>

                {{-- KOLOM KANAN: INFO PEKERJAAN --}}
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Detail Pekerjaan</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIP / Employee ID <span class="text-red-500">*</span></label>
                        <input type="text" name="employee_id" required value="{{ old('employee_id') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="Contoh: 2024001">
                        <p class="text-xs text-gray-500 mt-1">Nomor Induk Karyawan unik.</p>
                        @error('employee_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departemen <span class="text-red-500">*</span></label>
                            <input type="text" name="department" required value="{{ old('department') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Misal: IT, HR, Finance">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                            <input type="text" name="position" required value="{{ old('position') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm border p-2.5" placeholder="Misal: Staff, Manager">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor HP / WhatsApp</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                                +62
                            </span>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="block w-full min-w-0 flex-1 rounded-none rounded-r-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2.5" placeholder="81234567890">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Digunakan untuk menghubungi jika ada kendala aset.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection