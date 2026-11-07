<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-white h-screen overflow-hidden">

    <div class="h-full w-full flex flex-col md:flex-row p-3 md:p-4 gap-4 box-border">

        <!-- ================= KIRI: FORM LOGIN ================= -->
        <div class="w-full md:w-1/2 h-full flex flex-col justify-between px-6 md:px-12 py-4 overflow-y-auto">

            <div>
                <!-- Header Logo (Background hitam diubah jadi putih bersih) -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-white inline-block">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo Universitas IPWIJA" class="h-10 w-auto object-contain mix-blend-darken" onerror="this.src='https://via.placeholder.com/40?text=UNP'">
                    </div>
                </div>

                <!-- Judul -->
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Masuk</h1>
                <p class="text-xs text-gray-500 mb-6">Selamat datang di Sistem Manajemen Perpustakaan</p>

                <!-- Alert error / success -->
                @if ($errors->any())
                    <div class="mb-4 p-2.5 rounded-lg bg-red-50 text-red-600 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-2.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email / Tujuan</label>
                        <div class="relative">
                            <!-- Atribut name diubah dari "email" menjadi "tujuan" untuk menghindari error validasi backend -->
                            <input type="email" id="email" name="tujuan" value="{{ old('tujuan') }}" required autofocus
                                placeholder="Masukan Email Anda"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 pr-9 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="Masukan Password Anda"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 pr-9 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-right mt-1.5">
                            <!-- Tombol Lupa Password memicu Modal Pop-Up -->
                            <button type="button" onclick="openForgotPasswordModal()" class="text-[11px] text-red-600 font-semibold hover:underline focus:outline-none">Lupa Password</button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#0b4a80] hover:bg-[#08365e] text-white font-medium py-2 rounded-md transition text-xs shadow-sm mt-2">
                        Masuk
                    </button>

                    <p class="text-center text-[11px] text-gray-500 pt-1">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-emerald-500 font-semibold hover:underline">Daftar</a>
                    </p>
                </form>
            </div>

            <!-- Copyright Footer -->
            <p class="text-[11px] text-gray-600 font-medium mt-6">
                &copy; Universitas IPWIJA - {{ date('Y') }} - Masukkan NIM - Masukkan Nama Lengkap
            </p>
        </div>

        <!-- ================= KANAN: PANEL BIRU (ROUNDED) ================= -->
        <div class="hidden md:flex w-1/2 h-full bg-[#0b4a80] rounded-2xl flex-col justify-between items-center text-white p-10 relative">
            <div></div>

            <div class="text-center max-w-sm">
                <h2 class="text-xl font-bold leading-snug mb-2">
                    Cara termudah mengelola data perpustakaan digital
                </h2>
                <p class="text-xs text-blue-100/70 mb-6 font-light">
                    Masukkan kredensial Anda untuk mengakses sistem manajemen perpustakaan.
                </p>

                <!-- Mock Card Skeleton -->
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

    <!-- ================= MODAL LUPA PASSWORD (STEP 1) ================= -->
    <div id="forgotPasswordModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl relative animate-in fade-in zoom-in duration-200">
            <h3 class="text-base font-bold text-gray-900 mb-1">Lupa Password</h3>
            <p class="text-xs text-gray-500 mb-4">Masukkan email terdaftar Anda. Kami akan mengirimkan kode OTP untuk mereset password.</p>
            
            <!-- Form Kirim OTP -->
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <!-- INPUT HIDDEN INI DITAMBAHKAN AGAR LOLOS VALIDASI DI OtpController -->
                <input type="hidden" name="message" value="Permintaan Reset Password">

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email / Tujuan Terdaftar</label>
                    <input type="email" name="tujuan" required 
                        placeholder="nama@email.com"
                        class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:outline-none focus:border-[#0b4a80] placeholder-gray-300">
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeForgotPasswordModal()" class="px-4 py-2 bg-gray-100 text-gray-600 text-xs font-medium rounded-md hover:bg-gray-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#0b4a80] text-white text-xs font-medium rounded-md hover:bg-[#08365e] transition">Kirim OTP</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }

        // Fungsi untuk membuka Modal Pop-Up Lupa Password
        function openForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
        }

        // Fungsi untuk menutup Modal Pop-Up Lupa Password
        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
        }
    </script>
</body>
</html>