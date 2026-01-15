<?php

use App\Http\Controllers\PhoneAuthController;
use App\Http\Controllers\FirebaseExampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.phone.show');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [PhoneAuthController::class, 'show'])->name('login');
    Route::get('/auth/phone', [PhoneAuthController::class, 'show'])->name('auth.phone.show');
    Route::post('/auth/phone/verify', [PhoneAuthController::class, 'verify'])->name('auth.phone.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');

    Route::post('/auth/logout', [PhoneAuthController::class, 'logout'])->name('auth.logout');
});

Route::get('/firebase/health', [FirebaseExampleController::class, 'health'])->name('firebase.health');

require __DIR__ . '/admin.php';
