<?php

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

    // Astrologer Private (Role protected usually, but here just auth for now, can add middleware)
    Route::put('/astrologer/profile', [\App\Http\Controllers\AstrologerController::class, 'update']);
    Route::put('/astrologer/status', [\App\Http\Controllers\AstrologerController::class, 'toggleStatus']);
    // Calls
    Route::post('/call/initiate', [\App\Http\Controllers\CallController::class, 'initiate']);

    // Chat
    Route::post('/chat/initiate', [\App\Http\Controllers\ChatController::class, 'initiate']);
    Route::post('/chat/end', [\App\Http\Controllers\ChatController::class, 'end']);

    // AI Chat
    Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'sendMessage']);
    Route::get('/ai/history', [\App\Http\Controllers\AiChatController::class, 'getHistory']);

    // Withdrawals
    Route::post('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'store']); // Astrologer
    Route::get('/withdrawals', [\App\Http\Controllers\WithdrawalController::class, 'index']); // Admin
    Route::put('/withdrawals/{id}', [\App\Http\Controllers\WithdrawalController::class, 'updateStatus']); // Admin
});

// Webhooks
Route::post('/webhooks/phonepe', [PaymentController::class, 'callback']);
Route::post('/webhooks/callerdesk', [\App\Http\Controllers\CallController::class, 'webhook']);
Route::any('/payment/redirect', function () {
    return response()->json(['message' => 'Payment processed. Return to app.']);
});
