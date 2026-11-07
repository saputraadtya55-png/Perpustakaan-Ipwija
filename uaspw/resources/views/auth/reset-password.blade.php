<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Baru - Sistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans h-screen flex overflow-hidden">

    <!-- Sisi Kiri: Form Password Baru -->
    <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 lg:p-12 bg-white z-10 overflow-y-auto">
        <div>
            <div class="flex items-center gap-3">
                <a href="/">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo Universitas IPWIJA" class="h-16 w-auto object-contain">
                </a>
            </div>
        </div>

        <div class="max-w-md w-full mx-auto my-auto py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat Password Baru</h1>
                <p class="text-sm text-gray-500 mt-2">Silakan masukkan password baru untuk akun perpustakaan Anda.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" name="password" required autofocus
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        placeholder="Minimal 6 karakter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        placeholder="Ulangi password baru">
                </div>

                <button type="submit" 
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition duration-200">
                    Simpan Password Baru
                </button>
            </form>
        </div>

        <div></div>
    </div>

    <!-- Sisi Kanan: Panel Info Biru -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-800 p-12 flex-col justify-between relative overflow-hidden text-white">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-black/10 blur-2xl"></div>

        <div></div>
        
        <div class="z-10 max-w-lg">
            <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-semibold tracking-wider uppercase backdrop-blur-md">Pemulihan Akun</span>
            <h2 class="text-4xl font-extrabold mt-4 leading-tight">Lindungi Akses Anda</h2>
            <p class="mt-4 text-blue-100 text-base leading-relaxed">
                Gunakan kombinasi kata sandi yang kuat agar akun perpustakaan Universitas IPWIJA Anda tetap aman dari risiko keamanan yang tidak diinginkan.
            </p>
        </div>

        <div></div>
    </div>

</body>
</html>