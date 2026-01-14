<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AstrologerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', function () {
    return view('welcome');
// Auth Routes
Route::get('/login', [AuthController::class, 'userLoginView'])->name('login');
Route::get('/admin/login', [AuthController::class, 'adminLoginView'])->name('admin.login');
Route::get('/astrologer/login', [AuthController::class, 'astrologerLoginView'])->name('astrologer.login');

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login/firebase', [AuthController::class, 'loginWithFirebase'])->name('auth.login.firebase');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Portal
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::get('/wallet', [UserController::class, 'wallet'])->name('user.wallet');
});

// Admin Portal
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/astrologers', [AdminController::class, 'astrologers'])->name('astrologers');
});

// Astrologer Portal
Route::middleware(['auth', 'astrologer'])->prefix('astrologer')->name('astrologer.')->group(function () {
    Route::get('/dashboard', [AstrologerController::class, 'index'])->name('dashboard');
    Route::get('/schedule', [AstrologerController::class, 'schedule'])->name('schedule');
});
