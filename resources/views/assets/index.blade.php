@extends('layouts.main')

@section('container')
<div class="sm:flex sm:items-center sm:justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Manajemen User
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Daftar akun Admin dan Karyawan yang terdaftar.
        </p>
    </div>
    <div class="mt-4 sm:mt-0 sm:ml-4">
        <a href="/users/create" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
            Tambah User Baru
        </a>
    </div>
</div>

<div class="overflow-hidden bg-white shadow sm:rounded-lg border border-gray-200">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($users as $user)
                <tr>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500">
                                    <span class="font-medium leading-none text-white">{{ substr($user->name, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-gray-500 text-sm">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        @if($user->role == 'admin')
                            <span class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800">Admin</span>
                        @else
                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Karyawan</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                        <a href="/users/{{ $user->id }}/edit" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        
                        {{-- Tombol Hapus (Cegah hapus diri sendiri) --}}
                        @if(auth()->user()->id !== $user->id)
                            <form action="/users/{{ $user->id }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus user ini? Data aset yang dipegang akan menjadi NULL.')">
                                @method('delete')
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection