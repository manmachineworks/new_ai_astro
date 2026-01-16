<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PricingSettingsController;
use App\Http\Controllers\Admin\AdminAstrologerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CallController;

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Payments Audit
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

    // Astrologer Management
    Route::middleware('permission:view_users')->group(function () {
        Route::get('/astrologers', [AdminAstrologerController::class, 'index'])->name('astrologers.index');
        Route::get('/astrologers/{id}', [AdminAstrologerController::class, 'show'])->name('astrologers.show');
        Route::put('/astrologers/{id}/verify', [AdminAstrologerController::class, 'verify'])->name('astrologers.verify');
        Route::put('/astrologers/{id}/toggle-visibility', [AdminAstrologerController::class, 'toggleVisibility'])->name('astrologers.toggleVisibility');
        Route::put('/astrologers/{id}/toggle-account', [AdminAstrologerController::class, 'toggleAccount'])->name('astrologers.toggleAccount');

        // Calls Audit
        Route::get('/calls', [CallController::class, 'index'])->name('calls.index');
        Route::get('/calls/{id}', [CallController::class, 'show'])->name('calls.show');
    });

    // Pricing Settings
    Route::get('/pricing', [PricingSettingsController::class, 'index'])->name('pricing.index');
    Route::post('/pricing', [PricingSettingsController::class, 'update'])->name('pricing.update');
});
