<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PricingSettingsController;
use App\Http\Controllers\Admin\AdminAstrologerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ReportingController;

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [\App\Http\Controllers\PhoneAuthController::class, 'logout'])->name('logout');

    // Payments Audit
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

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
    Route::put('/pricing', [PricingSettingsController::class, 'update'])->name('pricing.update');

    // AI Chat Settings
    Route::get('/ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'index'])->name('ai.settings');
    Route::post('/ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'update'])->name('ai.settings.update');
    Route::get('/ai-reports', [App\Http\Controllers\Admin\AiSettingsController::class, 'reports'])->name('ai.reports');

    // Reporting & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportingController::class, 'dashboard'])->name('dashboard');
        Route::get('/revenue', [ReportingController::class, 'revenue'])->name('revenue');
        Route::get('/wallet-recharges', [ReportingController::class, 'recharges'])->name('recharges');
        Route::get('/calls', [ReportingController::class, 'calls'])->name('calls');
        Route::get('/chats', [ReportingController::class, 'chats'])->name('chats');
        Route::get('/ai-chats', [ReportingController::class, 'aiChats'])->name('ai_chats');
        Route::get('/astrologers', [ReportingController::class, 'astrologers'])->name('astrologers');
    });

    // Promo Campaigns
    Route::prefix('promos')->name('promos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PromoController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\PromoController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\PromoController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\PromoController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\PromoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\PromoController::class, 'update'])->name('update');
        Route::post('/{id}/toggle', [App\Http\Controllers\Admin\PromoController::class, 'toggle'])->name('toggle');
        Route::delete('/{id}', [App\Http\Controllers\Admin\PromoController::class, 'destroy'])->name('destroy');
    });

    // Referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReferralController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\ReferralController::class, 'show'])->name('show');
        Route::post('/{id}/override', [App\Http\Controllers\Admin\ReferralController::class, 'override'])->name('override');
        Route::get('/export/csv', [App\Http\Controllers\Admin\ReferralController::class, 'export'])->name('export');
    });

    // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('cancel');
    });

    // Support Tickets
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('reply');
        Route::post('/{id}/close', [App\Http\Controllers\Admin\SupportController::class, 'close'])->name('close');
        Route::post('/{id}/resolve', [App\Http\Controllers\Admin\SupportController::class, 'resolve'])->name('resolve');
    });

    // Disputes & Refunds
    Route::prefix('disputes')->name('disputes.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DisputeController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\DisputeController::class, 'show'])->name('show');
        Route::post('/{id}/request-info', [App\Http\Controllers\Admin\DisputeController::class, 'requestInfo'])->name('request_info');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\DisputeController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\DisputeController::class, 'reject'])->name('reject');
    });

});
