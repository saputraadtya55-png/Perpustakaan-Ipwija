<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        // 1. Validasi input tujuan (email) dan pastikan terdaftar di database
        $request->validate([
            'tujuan' => ['required', 'email', 'exists:users,email'],
        ], [
            'tujuan.exists' => 'Email tidak terdaftar di sistem kami.',
            'tujuan.required' => 'Field email wajib diisi.',
        ]);

        $tujuan = $request->input('tujuan');
        
        // Generate kode OTP acak 6 digit
        $otp = rand(100000, 999999);

        // Simpan email ke session untuk tahap verifikasi berikutnya
        session(['reset_email' => $tujuan]);

        // Simpan kode OTP ke kolom otp_code pada tabel users
        $user = User::where('email', $tujuan)->first();
        if ($user) {
            $user->update([
                'otp_code' => $otp,
            ]);
        }

        $pesan = 'Halo ' . ($user->name ?? 'User') . ', kode OTP untuk reset password kamu adalah: ' . $otp; 

        // 2. URL Webhook n8n diambil dinamis dari file .env (menggunakan jalur produksi)
        $urlN8n = env('N8N_WEBHOOK_URL');

        // 3. Kirim data ke n8n
        $response = Http::timeout(5)->post($urlN8n, [
            'metode_kirim' => 'email',
            'tujuan'       => $tujuan,
            'pesan'        => $pesan, 
        ]);

        // Cek jika n8n menolak/gagal merespons
        if ($response->failed()) {
            return back()->withErrors(['tujuan' => 'Gagal mengirim OTP ke server n8n. Periksa kembali koneksi webhook n8n.']);
        }

        // 4. Redirect langsung ke halaman form input OTP
        return redirect()->route('password.verify.form')->with('success', 'Kode OTP berhasil dikirim! Silakan masukkan kode.');
    }
}