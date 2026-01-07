<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIMASET PT Vitech Asia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full">
    <div class="flex min-h-full">
        
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Corporate Building">
            <div class="absolute inset-0 bg-indigo-900/80 mix-blend-multiply"></div>
            <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
                <h2 class="text-4xl font-bold mb-4">Join the Team</h2>
                <p class="text-lg text-indigo-100">
                    Daftarkan akun Anda untuk mulai mengakses sistem manajemen aset perusahaan. Pastikan data yang dimasukkan sesuai dengan ID Karyawan.
                </p>
            </div>
        </div>

        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <div>
                    <img class="h-16 w-auto" src="{{ asset('img/logoVitechAsia.png') }}" alt="PT Vitech Asia">
                    <h2 class="mt-8 text-2xl font-bold leading-9 tracking-tight text-gray-900">Buat Akun Baru</h2>
                </div>

                <div class="mt-10">
                    <form action="/register" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nama Lengkap</label>
                            <div class="mt-2">
                                <input id="name" name="name" type="text" required 
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Perusahaan</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" required 
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" required 
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>
                </div>
                
                <p class="mt-10 text-center text-sm text-gray-500">
                    Sudah punya akun? 
                    <a href="/login" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Login di sini</a>
                </p>

            </div>
        </div>
    </div>
</body>
</html>