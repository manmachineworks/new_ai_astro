<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AstrologerAppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

    // Payment
    Route::post('/wallet/recharge', [PaymentController::class, 'initiate']);

    // Astrologer Public
    Route::get('/astrologers', [\App\Http\Controllers\AstrologerController::class, 'index']);
    Route::get('/astrologers/{id}', [\App\Http\Controllers\AstrologerController::class, 'show']);
    Route::get('/astrologers/{id}/appointment-slots', [AppointmentController::class, 'slots']);

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
    Route::post('/chat/initiate', [\App\Http\Controllers\ChatController::class, 'initiate']);
    Route::post('/chat/end', [\App\Http\Controllers\ChatController::class, 'end']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments/hold', [AppointmentController::class, 'hold']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

    // AI Chat
    Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage']);
    Route::get('/ai/history', [\App\Http\Controllers\AiChatController::class, 'getHistory']);

    // Withdrawals
    Route::post('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'store']); // Astrologer
    Route::get('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'index']); // Admin
    Route::put('/withdrawals/{id}', [\App\Http\Controllers\WithdrawalController::class, 'updateStatus']); // Admin
    // Notifications
    Route::post('/devices/register', [\App\Http\Controllers\Api\DeviceTokenController::class, 'register']);
    Route::post('/devices/unregister', [\App\Http\Controllers\Api\DeviceTokenController::class, 'unregister']);
    Route::get('/notifications/preferences', [\App\Http\Controllers\Api\NotificationPreferenceController::class, 'show']);
    Route::put('/notifications/preferences', [\App\Http\Controllers\Api\NotificationPreferenceController::class, 'update']);
});

// Internal APIs (Secured by Secret)
Route::get('/internal/fcm-tokens', [\App\Http\Controllers\Api\InternalController::class, 'getFcmTokens']);

// Webhooks
// Webhooks
Route::post('/webhooks/phonepe', [\App\Http\Controllers\PaymentController::class, 'handleWebhook']);
Route::post('/webhooks/callerdesk', [\App\Http\Controllers\CallController::class, 'webhook']);
Route::any('/payment/redirect', [\App\Http\Controllers\PaymentController::class, 'handleRedirect']);
