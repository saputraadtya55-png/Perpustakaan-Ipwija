<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'is_active'          => false,
            'verification_token' => $token,
        ]);

        $verificationUrl = route('verify.email', ['token' => $token]);

        try {
            Http::post(env('N8N_WEBHOOK_REGISTER_URL', 'http://localhost:5678/webhook/register'), [
                'name'             => $user->name,
                'email'            => $user->email,
                'verification_url' => $verificationUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim webhook ke N8N: ' . $e->getMessage());
        }

        return redirect()->route('verification.notice');
    }

    public function verificationNotice()
    {
        return view('verify-notice');
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Tautan verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        $user->update([
            'is_active'          => true,
            'verification_token' => null,
            'email_verified_at'  => now(),
        ]);

        return redirect()->route('login')->with('success', 'Akun Anda berhasil diverifikasi dan sudah aktif! Silakan login.');
    }
}