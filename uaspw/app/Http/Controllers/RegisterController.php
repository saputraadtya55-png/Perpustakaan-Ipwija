<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    // Menampilkan halaman form register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses pendaftaran akun baru (Status Pending / is_active = 'pending' atau false)
    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Cek apakah email sudah terdaftar dan statusnya sudah aktif
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $existingUser->is_active === 'active') {
            return back()->withErrors(['email' => 'Email ini sudah terdaftar dan aktif. Silakan langsung login.'])->withInput();
        }

        // Jika email sudah ada tapi status masih pending, hapus data lama agar bisa daftar ulang
        if ($existingUser) {
            $existingUser->delete();
        }

        // 2. Generate token unik untuk verifikasi
        $token = substr(preg_replace('/[^a-zA-Z0-9]/', '', Str::random(10)), 0, 10);

        // 3. Simpan user dengan status is_active = 'pending' (tidak langsung aktif)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp_code' => $token, 
            'is_active' => 'pending', // Sesuai ketentuan soal: belum aktif sebelum verifikasi
        ]);

        // 4. Buat URL tautan verifikasi
        $verificationUrl = route('register.verify.link', ['token' => $token]);

        // 5. Kirim HTTP POST Request via Webhook ke N8N melalui tunnel Ngrok
        $pesan = 'Halo ' . $user->name . ', terima kasih telah mendaftar. Silakan klik tautan berikut untuk memverifikasi akun Anda: ' . $verificationUrl;
        
        // Mengambil URL n8n secara dinamis dari file .env
        $urlN8n = env('N8N_REGISTER_URL'); 
        
        try {
            Http::timeout(10)->post($urlN8n, [
                'tujuan' => $user->email,
                'pesan' => $pesan,
                'link' => $verificationUrl,
            ]);
        } catch (\Exception $e) {
            // Debugging jika koneksi ke n8n gagal
            dd($e->getMessage());
        }

        // 6. Mengarahkan user ke halaman informasi (Notice) bahwa tautan verifikasi telah dikirim ke email
        return redirect()->route('register.verify.notice');
    }

    // Menampilkan halaman informasi verifikasi
    public function showVerifyNotice()
    {
        return view('auth.verify-notice');
    }

    // Memproses klik tautan verifikasi dari email untuk mengubah status menjadi aktif
    public function verifyLink(Request $request, $token)
    {
        $user = User::where('otp_code', $token)->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Tautan verifikasi tidak valid atau sudah kedaluwarsa!']);
        }

        // Ubah status akun menjadi aktif (is_active = true / 'active') dan bersihkan token
        $user->update([
            'is_active' => 'active',
            'otp_code' => null,
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil diaktivasi! Silakan login.');
    }
}