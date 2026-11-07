<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('login');
    }

    // Proses login (Disesuaikan menggunakan 'tujuan' agar selaras dengan input form)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'tujuan'   => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Mapping input 'tujuan' ke kolom database 'email' untuk proses Auth
        $authData = [
            'email'    => $request->tujuan,
            'password' => $request->password,
        ];

        if (Auth::attempt($authData, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'tujuan' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('tujuan');
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('register');
    }

    // Proses register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan masuk ke akun Anda.');
    }

    // ==========================================
    // FITUR LUPA PASSWORD & VALIDASI OTP (STEP 1 - 4)
    // ==========================================

    // Step 1 & 2: Generate OTP & Kirim Webhook ke N8N
    public function sendOtp(Request $request)
    {
        // Disesuaikan dari 'email' ke 'tujuan' agar cocok dengan input modal lupa password
        $request->validate([
            'tujuan' => ['required', 'email', 'exists:users,email'],
        ], [
            'tujuan.exists' => 'Email tidak terdaftar di sistem kami.',
            'tujuan.required' => 'Field email wajib diisi.',
        ]);

        // Generate kode OTP acak 6 digit
        $otp = rand(100000, 999999);

        // Simpan ke kolom otp_code pada tabel users
        $user = User::where('email', $request->tujuan)->first();
        $user->update([
            'otp_code' => $otp,
        ]);

        // Simpan email ke session untuk tahap verifikasi berikutnya
        session(['reset_email' => $user->email]);

        // Step 2: Kirim webhook ke N8N
        $n8nWebhookUrl = 'http://localhost:5678/webhook-test/kirim-otp'; 

        $response = Http::timeout(5)->post($n8nWebhookUrl, [
            'metode'  => 'email',
            'tujuan'  => $user->email, 
            'pesan'   => 'Halo ' . $user->name . ', kode OTP untuk reset password kamu adalah: ' . $otp,
        ]);

        // Jika n8n gagal merespons atau belum di-listen
        if ($response->failed()) {
            return back()->withErrors(['tujuan' => 'Gagal mengirim OTP ke server n8n. Pastikan node n8n sudah di-Execute/Listen!']);
        }

        // Berhasil terkirim, arahkan ke halaman input kode OTP beserta data pop-up
        return redirect()->route('password.verify.form')
            ->with('success', 'Kode OTP telah dikirim melalui email.')
            ->with('otp_code_popup', $otp);
    }

    // Step 3 (A): Tampilkan halaman/form input OTP
    public function showVerifyOtp()
    {
        if (!session('reset_email')) {
            return redirect()->route('login')->withErrors(['tujuan' => 'Sesi reset password kedaluwarsa. Silakan ulangi.']);
        }
        return view('auth.verify-otp'); 
    }

    // Step 3 (B): Validasi kecocokan kode OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->where('otp_code', $request->otp_code)->first();

        if (!$user) {
            return back()->withErrors(['otp_code' => 'Kode OTP salah atau tidak valid.']);
        }

        // Jika valid, tandai di session bahwa user sudah lolos verifikasi OTP
        session(['otp_verified' => true]);

        return redirect()->route('password.reset.form');
    }

    // Step 4 (A): Tampilkan form Password Baru
    public function showResetPassword()
    {
        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('login')->withErrors(['tujuan' => 'Akses tidak sah. Silakan ulangi proses lupa password.']);
        }
        return view('auth.reset-password'); 
    }

    // Step 4 (B): Simpan password baru, kosongkan otp_code
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
                'otp_code' => null, // Kosongkan OTP agar tidak bisa dipakai ulang
            ]);
        }

        // Hapus session terkait reset
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan masuk dengan password baru.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}