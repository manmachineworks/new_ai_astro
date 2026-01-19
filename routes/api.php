<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AstrologerAppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ReportingController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('firebase.auth');

Route::middleware('firebase.auth')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // User Profile
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'show']);
    Route::put('/profile', [\App\Http\Controllers\UserProfileController::class, 'update']);

    // Payment
    Route::post('/wallet/recharge', [PaymentController::class, 'initiate']);

    // Astrologer Public
    Route::get('/astrologers', [\App\Http\Controllers\AstrologerController::class, 'index']);
    Route::get('/astrologers/{id}', [\App\Http\Controllers\AstrologerController::class, 'show']);
    Route::get('/astrologers/{id}/appointment-slots', [AppointmentController::class, 'slots']);
    Route::post('/astrologers/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'store']);

    // Astrologer Private (Role protected usually, but here just auth for now, can add middleware)
    Route::put('/astrologer/profile', [\App\Http\Controllers\AstrologerController::class, 'update']);
    Route::put('/astrologer/status', [\App\Http\Controllers\AstrologerController::class, 'toggleStatus']);
    Route::get('/astrologer/appointments', [AstrologerAppointmentController::class, 'index']);
    Route::post('/astrologer/appointments/{id}/confirm', [AstrologerAppointmentController::class, 'confirm']);
    Route::post('/astrologer/appointments/{id}/decline', [AstrologerAppointmentController::class, 'decline']);
    Route::post('/astrologer/appointments/{id}/cancel', [AstrologerAppointmentController::class, 'cancel']);
    Route::post('/astrologer/slots/{slot}/block', [AstrologerAppointmentController::class, 'blockSlot']);
    Route::post('/astrologer/slots/{slot}/unblock', [AstrologerAppointmentController::class, 'unblockSlot']);
    // Calls
    Route::post('/call/initiate', [\App\Http\Controllers\CallController::class, 'initiate']);

    // Chat
    // Chat
    // Route::post('/chat/initiate', [\App\Http\Controllers\ChatController::class, 'initiate']);
    // Route::post('/chat/end', [\App\Http\Controllers\ChatController::class, 'end']);
    // Route::post('/chat/{session}/report', [\App\Http\Controllers\ChatController::class, 'report']);

    // Reviews
    Route::put('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy']);
    Route::put('/reviews/{review}/status', [\App\Http\Controllers\ReviewController::class, 'moderate'])
        ->middleware('permission:manage_reviews');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments/hold', [AppointmentController::class, 'hold']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

    // AI Chat
    // AI Chat
    // Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage']);
    // Route::get('/ai/history', [\App\Http\Controllers\AiChatController::class, 'getHistory']);

    // Withdrawals
    Route::post('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'store']); // Astrologer
    Route::get('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'index']); // Admin
    Route::put('/withdrawals/{id}', [\App\Http\Controllers\WithdrawalController::class, 'updateStatus']); // Admin
    // Notifications
    Route::post('/devices/register', [\App\Http\Controllers\Api\DeviceTokenController::class, 'register']);
    Route::post('/devices/unregister', [\App\Http\Controllers\Api\DeviceTokenController::class, 'unregister']);
    Route::get('/notifications/preferences', [\App\Http\Controllers\Api\NotificationPreferenceController::class, 'show']);
    Route::put('/notifications/preferences', [\App\Http\Controllers\Api\NotificationPreferenceController::class, 'update']);

    // In-App Inbox
    Route::prefix('inbox')->group(function () {
        Route::get('/notifications', [App\Http\Controllers\Api\InboxController::class, 'index']);
        Route::post('/notifications/read-all', [App\Http\Controllers\Api\InboxController::class, 'markAllRead']);
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\InboxController::class, 'markRead']);
        Route::post('/notifications/{id}/archive', [App\Http\Controllers\Api\InboxController::class, 'archive']);

        // Tracking
        Route::post('/notifications/{id}/opened', [App\Http\Controllers\Api\TrackingController::class, 'openInbox']);
        Route::post('/push/opened', [App\Http\Controllers\Api\TrackingController::class, 'openPush']);
    });

    // Admin Reporting APIs
    Route::prefix('admin/reports')->middleware('permission:view_finance')->group(function () {
        Route::get('/dashboard', [ReportingController::class, 'dashboard']);
        Route::get('/revenue', [ReportingController::class, 'revenue']);
        Route::get('/revenue/items', [ReportingController::class, 'revenueItems']);
        Route::get('/recharges', [ReportingController::class, 'recharges']);
        Route::get('/calls', [ReportingController::class, 'calls']);
        Route::get('/chats', [ReportingController::class, 'chats']);
        Route::get('/ai', [ReportingController::class, 'aiChats']);
        Route::get('/astrologers', [ReportingController::class, 'astrologers']);
        Route::get('/appointments', [ReportingController::class, 'appointments']);
        Route::get('/refunds', [ReportingController::class, 'refunds']);
    });
});

// Public reviews
Route::get('/astrologers/{id}/reviews', [\App\Http\Controllers\ReviewController::class, 'index']);

// Internal APIs (Secured by Secret)
Route::get('/internal/fcm-tokens', [\App\Http\Controllers\Api\InternalController::class, 'getFcmTokens']);

// Webhooks
// Webhooks
Route::post('/webhooks/phonepe', [\App\Http\Controllers\Astrologer\Webhook\PhonePeWebhookController::class, 'handle']);
Route::post('/webhooks/callerdesk', [\App\Http\Controllers\Astrologer\Webhook\CallerDeskWebhookController::class, 'handle']);
Route::any('/payment/redirect', [\App\Http\Controllers\PaymentController::class, 'handleRedirect']);
