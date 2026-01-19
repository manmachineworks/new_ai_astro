<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PricingSettingsController;
use App\Http\Controllers\Admin\AdminAstrologerController;
use App\Http\Controllers\Admin\ChatModerationController;
use App\Http\Controllers\Admin\ChatBannedWordController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ReportingController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminUserManagementController;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\PaymentsController as FinancePaymentsController;
use App\Http\Controllers\Admin\Finance\WalletsController as FinanceWalletsController;

// Guest Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth', 'admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // 1. Admin Management (Super Admin Only)
    Route::middleware('permission:manage_roles')->group(function () {
        Route::resource('admin-users', AdminUserManagementController::class);
        Route::post('admin-users/{admin}/toggle', [AdminUserManagementController::class, 'toggleStatus'])->name('admin-users.toggle');
        Route::post('admin-users/{admin}/force-logout', [AdminUserManagementController::class, 'forceLogout'])->name('admin-users.force-logout');
        Route::resource('roles', \App\Http\Controllers\Admin\AdminRoleController::class)->names('roles');
    });

    // 2. User Management
    Route::middleware('permission:view_users')->group(function () {
        Route::post('/users/bulk-action', [\App\Http\Controllers\Admin\AdminUserController::class, 'bulkAction'])->name('users.bulk_action')->middleware('permission:manage_users');
        Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class)->names('users')->only(['index', 'show', 'edit', 'update']);
        Route::post('/users/{user}/toggle', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggle'])->name('users.toggle')->middleware('permission:manage_users');
        Route::post('/users/{user}/block', [\App\Http\Controllers\Admin\AdminUserController::class, 'block'])->name('users.block')->middleware('permission:manage_users');
        Route::post('/users/{user}/unblock', [\App\Http\Controllers\Admin\AdminUserController::class, 'unblock'])->name('users.unblock')->middleware('permission:manage_users');
        Route::post('/users/{user}/ai-chat/block', [\App\Http\Controllers\Admin\AdminUserController::class, 'restrictAiChat'])->name('users.ai_chat.block')->middleware('permission:manage_users');
        Route::post('/users/{user}/ai-chat/unblock', [\App\Http\Controllers\Admin\AdminUserController::class, 'liftAiChatRestriction'])->name('users.ai_chat.unblock')->middleware('permission:manage_users');
    });

    // 3. Astrologer Management
    Route::middleware('permission:view_astrologers')->group(function () {
        Route::post('/astrologers/bulk-action', [AdminAstrologerController::class, 'bulkAction'])->name('astrologers.bulk_action')->middleware('permission:manage_astrologers');
        Route::get('/astrologers', [AdminAstrologerController::class, 'index'])->name('astrologers.index');
        Route::get('/astrologers/{id}', [AdminAstrologerController::class, 'show'])->name('astrologers.show');

        Route::middleware('permission:verify_astrologers')->group(function () {
            Route::put('/astrologers/{id}/verify', [AdminAstrologerController::class, 'verify'])->name('astrologers.verify');
            Route::put('/astrologers/{id}/toggle-account', [AdminAstrologerController::class, 'toggleAccount'])->name('astrologers.toggleAccount');
        });

        Route::put('/astrologers/{id}/toggle-visibility', [AdminAstrologerController::class, 'toggleVisibility'])->name('astrologers.toggleVisibility')->middleware('permission:toggle_astrologer_visibility');
        Route::put('/astrologers/{id}/profile', [AdminAstrologerController::class, 'updateProfile'])->name('astrologers.profile.update')->middleware('permission:manage_astrologers');
        Route::put('/astrologers/{id}/services', [AdminAstrologerController::class, 'updateServices'])->name('astrologers.services.update')->middleware('permission:manage_astrologers');
        Route::post('/astrologers/{id}/availability/rules', [AdminAstrologerController::class, 'storeAvailabilityRule'])->name('astrologers.availability.rules.store')->middleware('permission:manage_astrologers');
        Route::delete('/astrologers/{id}/availability/rules/{rule}', [AdminAstrologerController::class, 'deleteAvailabilityRule'])->name('astrologers.availability.rules.delete')->middleware('permission:manage_astrologers');
        Route::post('/astrologers/{id}/availability/exceptions', [AdminAstrologerController::class, 'storeAvailabilityException'])->name('astrologers.availability.exceptions.store')->middleware('permission:manage_astrologers');
        Route::delete('/astrologers/{id}/availability/exceptions/{exception}', [AdminAstrologerController::class, 'deleteAvailabilityException'])->name('astrologers.availability.exceptions.delete')->middleware('permission:manage_astrologers');
    });

    // 4. Finance (Payments & Wallets)
    Route::middleware('permission:view_finance')->group(function () {
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

        // Wallets
        Route::get('/wallets', [\App\Http\Controllers\Admin\AdminWalletController::class, 'index'])->name('wallets.index');
        Route::get('/wallets/{id}', [\App\Http\Controllers\Admin\AdminWalletController::class, 'show'])->name('wallets.show');

        Route::middleware('permission:wallet_credit')->post('/wallets/{id}/recharge', [\App\Http\Controllers\Admin\AdminWalletController::class, 'recharge'])->name('wallets.recharge');

        // Finance Ops
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/payments', [FinancePaymentsController::class, 'index'])->name('payments.index')->middleware('permission:manage_payments');
            Route::get('/payments/export', [FinancePaymentsController::class, 'export'])->name('payments.export')->middleware('permission:export_finance');
            Route::get('/payments/{paymentOrder}', [FinancePaymentsController::class, 'show'])->name('payments.show')->middleware('permission:manage_payments');
            Route::post('/payments/{paymentOrder}/recheck', [FinancePaymentsController::class, 'recheck'])->name('payments.recheck')->middleware('permission:manage_payments');
            Route::post('/payments/{paymentOrder}/retry-webhook', [FinancePaymentsController::class, 'retryWebhook'])->name('payments.retry_webhook')->middleware('permission:manage_payments');
            Route::post('/payments/{paymentOrder}/note', [FinancePaymentsController::class, 'updateNote'])->name('payments.note')->middleware('permission:manage_payments');

            Route::get('/wallets', [FinanceWalletsController::class, 'index'])->name('wallets.index');
            Route::get('/wallets/export', [FinanceWalletsController::class, 'export'])->name('wallets.export')->middleware('permission:export_finance');
            Route::get('/wallets/{user}', [FinanceWalletsController::class, 'show'])->name('wallets.show');
            Route::post('/wallets/{user}/adjust', [FinanceWalletsController::class, 'adjust'])->name('wallets.adjust')->middleware('permission:wallet_adjustments');

            Route::get('/earnings', [FinanceController::class, 'earnings'])->name('earnings.index')->middleware('permission:manage_payouts');
            Route::get('/refunds', [FinanceController::class, 'refunds'])->name('refunds.index')->middleware('permission:issue_refunds');
            Route::get('/commission-settings', [FinanceController::class, 'commissions'])->name('commissions.index')->middleware('permission:manage_commissions');
            Route::get('/exports', [FinanceController::class, 'exports'])->name('exports.index')->middleware('permission:export_finance');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportingController::class, 'dashboard'])->name('dashboard');
            Route::get('/revenue', [ReportingController::class, 'revenue'])->name('revenue');
            Route::get('/revenue/items', [ReportingController::class, 'revenueItems'])->name('revenue.items');
            Route::get('/export', [ReportingController::class, 'export'])->name('export');
            Route::get('/wallet-recharges', [ReportingController::class, 'recharges'])->name('recharges');
            Route::get('/calls', [ReportingController::class, 'calls'])->name('calls');
            Route::get('/chats', [ReportingController::class, 'chats'])->name('chats');
            Route::get('/ai', [ReportingController::class, 'aiChats'])->name('ai');
            Route::get('/astrologers', [ReportingController::class, 'astrologers'])->name('astrologers');
            Route::get('/refunds', [ReportingController::class, 'refunds'])->name('refunds');
        });
    });

    // 5. Communications (Calls & Chats)
    Route::middleware('permission:view_calls')->group(function () {
        Route::get('/calls', [CallController::class, 'index'])->name('calls.index');
        Route::get('/calls/{id}', [CallController::class, 'show'])->name('calls.show');
    });

    Route::middleware('permission:view_chats')->group(function () {
        Route::get('/chats', [\App\Http\Controllers\Admin\AdminChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{id}', [\App\Http\Controllers\Admin\AdminChatController::class, 'show'])->name('chats.show');
    });

    Route::middleware('permission:view_ai_chats')->group(function () {
        Route::get('/ai-chats', [\App\Http\Controllers\Admin\AdminAiChatController::class, 'index'])->name('ai_chats.index');
        Route::get('/ai-chats/{id}', [\App\Http\Controllers\Admin\AdminAiChatController::class, 'show'])->name('ai_chats.show');
    });

    // Moderation
    Route::middleware('permission:manage_chats')->prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/chats', [ChatModerationController::class, 'index'])->name('chats.index');
        Route::post('/chats/{thread}/mute', [ChatModerationController::class, 'mute'])->name('chats.mute');
        Route::post('/chats/{thread}/unmute/{target}', [ChatModerationController::class, 'unmute'])->name('chats.unmute');

        Route::get('/banned-words', [ChatBannedWordController::class, 'index'])->name('banned_words.index');
        Route::post('/banned-words', [ChatBannedWordController::class, 'store'])->name('banned_words.store');
        Route::post('/banned-words/{word}/toggle', [ChatBannedWordController::class, 'toggle'])->name('banned_words.toggle');
        Route::delete('/banned-words/{word}', [ChatBannedWordController::class, 'destroy'])->name('banned_words.destroy');
    });

    Route::middleware('permission:manage_reviews')->group(function () {
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::put('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // 6. Settings & System (Super Admin / Specific Permissions)
    Route::middleware('permission:manage_ai_settings')->group(function () {
        Route::get('/ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'index'])->name('ai.settings');
        Route::post('/ai-settings', [App\Http\Controllers\Admin\AiSettingsController::class, 'update'])->name('ai.settings.update');
    });

    Route::middleware('permission:manage_commissions')->group(function () {
        Route::get('/pricing', [PricingSettingsController::class, 'index'])->name('pricing.index');
        Route::put('/pricing', [PricingSettingsController::class, 'update'])->name('pricing.update');
    });

    // System Logs
    Route::prefix('system')->name('system.')->group(function () {
        Route::middleware('permission:view_webhooks')->group(function () {
            Route::get('/webhooks', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'index'])->name('webhooks.index');
            Route::get('/webhooks/{id}', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'show'])->name('webhooks.show');
            Route::post('/webhooks/{id}/retry', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'retry'])->name('webhooks.retry')->middleware('permission:retry_webhooks');
        });

        Route::middleware('permission:view_audit_logs')->group(function () {
            Route::get('/audit-logs', [\App\Http\Controllers\Admin\AdminAuditLogController::class, 'index'])->name('audit_logs.index');
            Route::get('/audit-logs/{id}', [\App\Http\Controllers\Admin\AdminAuditLogController::class, 'show'])->name('audit_logs.show');
        });
    });

    // CMS (Ops Admin)
    Route::middleware('permission:manage_content')->prefix('cms')->name('cms.')->group(function () {
        Route::resource('pages', \App\Http\Controllers\Admin\CmsPageController::class);
        Route::resource('banners', \App\Http\Controllers\Admin\CmsBannerController::class)->except(['show']);
        Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class)->except(['show']);

        Route::get('/featured', [\App\Http\Controllers\Admin\FeaturedAstrologerController::class, 'index'])->name('featured.index');
        Route::post('/featured', [\App\Http\Controllers\Admin\FeaturedAstrologerController::class, 'store'])->name('featured.store');
        Route::delete('/featured/{featured}', [\App\Http\Controllers\Admin\FeaturedAstrologerController::class, 'destroy'])->name('featured.destroy');
        Route::post('/featured/reorder', [\App\Http\Controllers\Admin\FeaturedAstrologerController::class, 'updateOrder'])->name('featured.reorder');

        Route::resource('blog/posts', \App\Http\Controllers\Admin\BlogController::class, ['as' => 'blog']);
        Route::resource('blog/categories', \App\Http\Controllers\Admin\BlogCategoryController::class, ['as' => 'blog']);
    });

    // Legacy / Other routes (Gate later if needed)
    Route::prefix('promos')->name('promos.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PromoController::class, 'index'])->name('index');
        // ... promos ...
    });

    // Appointments
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
        // ... appointments ...
    });

    // Support Tickets & Complaints
    Route::middleware('permission:manage_reviews')->prefix('support')->name('support.')->group(function () {
        Route::get('/', [SupportController::class, 'index'])->name('index');
        Route::get('/{id}', [SupportController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [SupportController::class, 'reply'])->name('reply');
        Route::post('/{id}/close', [SupportController::class, 'close'])->name('close');
        Route::post('/{id}/resolve', [SupportController::class, 'resolve'])->name('resolve');
    });

    Route::middleware('permission:manage_reviews')->prefix('disputes')->name('disputes.')->group(function () {
        Route::get('/', [DisputeController::class, 'index'])->name('index');
        Route::get('/{id}', [DisputeController::class, 'show'])->name('show');
        Route::post('/{id}/request-info', [DisputeController::class, 'requestInfo'])->name('request_info');
        Route::post('/{id}/approve', [DisputeController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DisputeController::class, 'reject'])->name('reject');
    });
});
