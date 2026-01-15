<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMASET PT Vitech Asia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="h-full">
    <div class="flex min-h-full">
        
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Vitech Asia Office Background">
            <div class="absolute inset-0 bg-slate-900/80 mix-blend-multiply"></div>
            <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
                <h2 class="text-4xl font-bold mb-4">IT Asset Management System</h2>
                <p class="text-lg text-slate-300">
                    Sistem terintegrasi untuk pengelolaan, pelacakan, dan audit inventaris aset IT di lingkungan PT Vitech Asia.
                </p>
                <div class="mt-8">
                    <p class="text-sm font-semibold text-slate-400">© 2026 PT Vitech Asia. All rights reserved.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <div>
                    <img class="h-16 w-auto" src="<?php echo e(asset('img/logoVitechAsia.png')); ?>" alt="PT Vitech Asia">
                    <h2 class="mt-8 text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to SIMASET</h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500">
                        Masuk untuk mengelola inventaris perusahaan.
                    </p>
                </div>

                <div class="mt-10">
                    <?php if(session()->has('loginError')): ?>
                    <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200 animate-pulse">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Login Gagal</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p><?php echo e(session('loginError')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form action="/login" method="POST" class="space-y-6">
                        <?php echo csrf_field(); ?>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Address</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required value="<?php echo e(old('email')); ?>"
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                                    placeholder="admin@vitech.asia">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required 
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium leading-6 text-gray-900">Kode Keamanan</label>
                            <div class="mt-2 flex gap-2">
                                <div class="relative flex-1" id="captcha-img-container">
                                    
                                    <?php echo captcha_img('flat'); ?>

                                </div>
                                <button type="button" class="btn-refresh bg-gray-100 hover:bg-gray-200 text-gray-600 p-2 rounded-md border border-gray-300 transition" title="Ganti Gambar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2">
                                <input type="text" name="captcha" id="captcha" required placeholder="Masukkan kode di atas"
                                    class="block w-full rounded-md border-0 py-1.5 shadow-sm ring-1 ring-inset <?php echo e($errors->has('captcha') ? 'ring-red-500 focus:ring-red-600' : 'ring-gray-300 focus:ring-indigo-600'); ?> placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6 px-3">
                                
                                <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="remember-me" class="ml-3 block text-sm leading-6 text-gray-700">Remember me</label>
                            </div>

                            <div class="text-sm leading-6">
                                <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Lupa password?</a>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                                Masuk ke Dashboard
                            </button>
                        </div>
                    </form>
                </div>
                
                <p class="mt-10 text-center text-sm text-gray-500">
                    Karyawan baru? 
                    <a href="/register" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Daftarkan akun di sini</a>
                </p>

            </div>
        </div>
    </div>

    
    <script type="text/javascript">
        $(".btn-refresh").click(function(){
            $.ajax({
                type:'GET',
                url:'/refresh-captcha',
                success:function(data){
                    $("#captcha-img-container").html(data.captcha);
                }
            });
        });
    </script>
</body>
</html><?php /**PATH C:\laragon\www\simaset_fix\resources\views/auth/login.blade.php ENDPATH**/ ?>