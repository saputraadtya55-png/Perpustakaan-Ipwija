<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP Lupa Password - Sistem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 font-sans h-screen flex overflow-hidden">

    <!-- Sisi Kiri: Form Verifikasi OTP -->
    <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 lg:p-12 bg-white z-10 overflow-y-auto">
        <!-- Logo di kiri atas -->
        <div>
            <div class="flex items-center gap-3">
                <a href="/">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo Universitas IPWIJA" class="h-16 w-auto object-contain">
                </a>
            </div>
        </div>

        <!-- Konten Form -->
        <div class="max-w-md w-full mx-auto my-auto py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Verifikasi Kode OTP</h1>
                <p class="text-sm text-gray-500 mt-2">Masukkan 6 digit kode OTP yang telah dikirimkan ke email Anda untuk mereset password.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Verifikasi OTP Lupa Password -->
            <form action="{{ route('password.verify') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode OTP</label>
                    <input type="text" name="otp_code" required maxlength="6" autofocus
                        class="w-full px-4 py-3 text-center tracking-widest text-2xl font-bold rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        placeholder="• • • • • •">
                </div>

                <button type="submit" 
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition duration-200">
                    Verifikasi OTP
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
            <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-semibold tracking-wider uppercase backdrop-blur-md">Reset Password</span>
            <h2 class="text-4xl font-extrabold mt-4 leading-tight">Keamanan Akun Perpustakaan</h2>
            <p class="mt-4 text-blue-100 text-base leading-relaxed">
                Masukkan kode rahasia yang dikirim melalui n8n untuk melanjutkan proses pembuatan password baru akun perpustakaan Universitas IPWIJA Anda.
            </p>
        </div>

        <div></div>
    </div>

</body>
</html>