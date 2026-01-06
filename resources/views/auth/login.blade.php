<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <title>Login Page</title>
</head>
<body class="bg-gradient-to-br from-purple-600 via-blue-500 to-teal-400 h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white/20 backdrop-blur-lg border border-white/30 p-8 rounded-2xl shadow-2xl">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white">Selamat Datang</h2>
            <p class="text-gray-200 text-sm mt-2">Silakan masuk ke akun Anda</p>
        </div>

        @if(session()->has('success'))
        <div class="bg-green-500/80 text-white p-3 rounded-lg mb-4 text-center text-sm font-bold">
            {{ session('success') }}
        </div>
        @endif

        @if(session()->has('loginError'))
        <div class="bg-red-500/80 text-white p-3 rounded-lg mb-4 text-center text-sm font-bold">
            {{ session('loginError') }}
        </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-5">
                <label for="email" class="block text-white text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-3 rounded-lg bg-white/50 border-none focus:ring-2 focus:ring-purple-300 text-gray-900 placeholder-gray-600" placeholder="nama@email.com" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-white text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-3 rounded-lg bg-white/50 border-none focus:ring-2 focus:ring-purple-300 text-gray-900" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-lg transition duration-300 shadow-lg transform hover:scale-105">
                Masuk Sekarang
            </button>
        </form>

        <p class="text-center text-white mt-6 text-sm">
            Belum punya akun? <a href="/register" class="font-bold underline hover:text-purple-200">Daftar di sini</a>
        </p>
    </div>

</body>
</html>