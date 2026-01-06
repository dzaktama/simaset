<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <title>Register Page</title>
</head>
<body class="bg-gradient-to-tl from-pink-500 via-red-500 to-yellow-500 h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white/20 backdrop-blur-lg border border-white/30 p-8 rounded-2xl shadow-2xl">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white">Buat Akun Baru</h2>
            <p class="text-gray-100 text-sm mt-2">Gabung bersama kami sekarang</p>
        </div>

        <form action="/register" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-4 py-3 rounded-lg bg-white/50 border-none focus:ring-2 focus:ring-yellow-300 text-gray-900" placeholder="Nama Kamu" required>
            </div>

            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-lg bg-white/50 border-none focus:ring-2 focus:ring-yellow-300 text-gray-900" placeholder="nama@email.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-white text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-lg bg-white/50 border-none focus:ring-2 focus:ring-yellow-300 text-gray-900" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-white text-red-600 font-bold py-3 rounded-lg hover:bg-gray-100 transition duration-300 shadow-lg transform hover:scale-105">
                Daftar
            </button>
        </form>

        <p class="text-center text-white mt-6 text-sm">
            Sudah punya akun? <a href="/login" class="font-bold underline hover:text-yellow-200">Login aja</a>
        </p>
    </div>

</body>
</html>