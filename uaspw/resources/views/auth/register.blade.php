<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Informasi Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-white h-screen overflow-hidden">

    <div class="h-full w-full flex flex-col md:flex-row p-3 md:p-4 gap-4 box-border">

        <!-- ================= KIRI: FORM REGISTER ================= -->
        <div class="w-full md:w-1/2 h-full flex flex-col justify-between px-6 md:px-12 py-4 overflow-y-auto">

            <div>
                <!-- Header Logo -->
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo Universitas IPWIJA" class="h-10 w-auto object-contain mix-blend-multiply" onerror="this.src='https://via.placeholder.com/40?text=UNP'">
                </div>

                <!-- Judul -->
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Daftar</h1>
                <p class="text-xs text-gray-500 mb-5">Buat akun untuk mengakses Sistem Manajemen Perpustakaan</p>

                <!-- Alert Error -->
                @if ($errors->any())
                    <div class="mb-4 p-2.5 rounded-lg bg-red-50 text-red-600 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('register.submit') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="Masukan Nama Lengkap Anda"
                            class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            placeholder="Masukan Email Anda"
                            class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="Masukan Password Anda"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 pr-9 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                            <button type="button" onclick="togglePassword('password', 'eyeIcon1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                placeholder="Konfirmasi Password Anda"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 pr-9 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                            <button type="button" onclick="togglePassword('password_confirmation', 'eyeIcon2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#0b4a80] hover:bg-[#08365e] text-white font-medium py-2 rounded-md transition text-xs shadow-sm mt-3">
                        Daftar
                    </button>

                    <p class="text-center text-[11px] text-gray-500 pt-1">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-[#0b4a80] font-semibold hover:underline">Masuk</a>
                    </p>
                </form>
            </div>

            <!-- Copyright Bottom (Sudah ditambah NIM dan Nama Lengkap) -->
            <p class="text-[11px] text-gray-600 font-medium mt-4">
                &copy; Universitas IPWIJA - {{ date('Y') }} - Masukkan NIM - Masukkan Nama Lengkap
            </p>
        </div>

        <!-- ================= KANAN: PANEL BIRU ================= -->
        <div class="hidden md:flex w-1/2 h-full bg-[#0b4a80] rounded-2xl flex-col justify-between items-center text-white p-10 relative">
            <div></div>

            <div class="text-center max-w-sm">
                <h2 class="text-xl font-bold leading-snug mb-2">
                    Cara termudah mengelola data perpustakaan digital
                </h2>
                <p class="text-xs text-blue-100/70 mb-6 font-light">
                    Masukkan kredensial Anda untuk mengakses sistem manajemen perpustakaan.
                </p>

                <!-- Mock Card -->
                <div class="bg-white/10 border border-white/20 rounded-xl p-4 text-left backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[9px] font-bold text-white/80 tracking-wider">
                            SISTEM INFORMASI PERPUSTAKAAN - UAS PEMROGRAMAN WEB
                        </p>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    </div>
                    <div class="h-1 bg-white/20 rounded-full mb-2 w-full"></div>
                    <div class="h-1 bg-white/20 rounded-full w-2/3"></div>
                </div>
            </div>

            <!-- Tech Stack Footer -->
            <div class="flex gap-4 text-[9px] font-bold tracking-widest text-white/50">
                <span>LARAVEL</span>
                <span>MYSQL</span>
                <span>REDIS</span>
                <span>ELASTICSEARCH</span>
                <span>N8N</span>
                <span>NGROK</span>
            </div>
        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const pwd = document.getElementById(inputId);
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>