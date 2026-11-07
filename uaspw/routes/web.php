<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController; 
use App\Http\Controllers\BukuController;
use App\Http\Controllers\OtpController;

// ============ ROUTE UTAMA ============
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ============ AUTH ROUTES (GUEST ONLY) ============
Route::middleware('guest')->group(function () {
    // Form & Proses Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Form & Proses Register
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

    // Halaman Informasi Verifikasi Register (Notice) - Sesuai Soal
    Route::get('/register/verify-notice', [RegisterController::class, 'showVerifyNotice'])->name('register.verify.notice');

    // Route Klik Tautan Verifikasi dari Email
    Route::get('/register/verify/{token}', [RegisterController::class, 'verifyLink'])->name('register.verify.link');

    // Route Lupa Password & OTP
    Route::post('/forgot-password/send-otp', [OtpController::class, 'sendOtp'])->name('password.email');
    Route::get('/forgot-password/verify', [AuthController::class, 'showVerifyOtp'])->name('password.verify.form');
    Route::post('/forgot-password/verify', [AuthController::class, 'verifyOtp'])->name('password.verify');
    
    Route::get('/forgot-password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ============ PROTECTED ROUTES (MUST BE LOGGED IN) ============
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Utama
    Route::get('/dashboard', [BukuController::class, 'index'])->name('dashboard');

    // CRUD Buku (Update & Delete tetap butuh auth)
    Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');
});

// ============ WEBHOOK / PUBLIC API (UNTUK N8N) ============
Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');