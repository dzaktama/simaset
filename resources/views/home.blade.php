@extends('layouts.main')

@section('container')
    <h1 class="text-3xl font-bold mb-6">Daftar Pengguna</h1>

    <a href="/users/create" class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out mb-4">
        + Tambah User Baru
    </a>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead>
              <tr>
                  <th class="px-6 py-3">No</th>
                  <th class="px-6 py-3">Nama Lengkap</th>
                  <th class="px-6 py-3">Email</th>
                  <th class="px-6 py-3">Aksi</th> </tr>
          </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4 gap-2 space-x-2 flex">
                      <a href="/users/{{ $user->id }}/edit" class="text-yellow-600 hover:text-yellow-900 font-bold">Edit</a>
                        <form action="/users/{{ $user->id }}" method="POST" onsubmit="return confirm('Yakin mau hapus?')">
                            @csrf
                            @method('DELETE') <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection