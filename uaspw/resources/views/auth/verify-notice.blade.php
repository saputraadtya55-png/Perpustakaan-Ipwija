<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Sistem Informasi Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 h-screen flex items-center justify-center overflow-hidden">

    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-lg border border-slate-100 text-center">
        <!-- Icon Email / Info -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-50 text-blue-600 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">Cek Email Anda</h2>
        <p class="text-sm text-gray-600 mb-6">
            Tautan atau kode verifikasi telah dikirim ke email Anda. Silakan periksa inbox atau folder spam untuk melanjutkan proses aktivasi akun.
        </p>

        <div class="space-y-3">
            <a href="{{ route('login') }}" class="w-full inline-block bg-blue-600 text-white font-medium py-2.5 px-4 rounded-xl hover:bg-blue-700 transition duration-200">
                Kembali ke Halaman Masuk
            </a>
        </div>
    </div>

</body>
</html>