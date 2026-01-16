<?php

use App\Http\Controllers\PhoneAuthController;
use App\Http\Controllers\FirebaseExampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/auth/login', function () {
    return redirect()->route('auth.phone.show');
})->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [PhoneAuthController::class, 'show'])->name('login');
    Route::get('/auth/phone', [PhoneAuthController::class, 'show'])->name('auth.phone.show');
    Route::post('/auth/phone/verify', [PhoneAuthController::class, 'verify'])->name('auth.phone.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/user/dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/calls', [\App\Http\Controllers\UserCallController::class, 'index'])->name('user.calls');

    // Chat
    Route::get('/user/chats', [App\Http\Controllers\ChatController::class, 'index'])->name('user.chats');
    Route::get('/user/chats/{conversationId}', [App\Http\Controllers\ChatController::class, 'show'])->name('user.chats.show');
    Route::post('/chats/start', [App\Http\Controllers\ChatController::class, 'start'])->name('chats.start');
    Route::get('/firebase/token', [App\Http\Controllers\ChatController::class, 'firebaseToken'])->name('firebase.token');
    Route::post('/chats/{session}/authorize-send', [App\Http\Controllers\ChatController::class, 'authorizeSend'])->name('chats.authorize');
    Route::post('/chats/{session}/confirm-sent', [App\Http\Controllers\ChatController::class, 'confirmSent'])->name('chats.confirm');
    Route::post('/devices/register-token', [App\Http\Controllers\DeviceController::class, 'registerToken'])->name('devices.register');

    Route::middleware('role:Astrologer')->group(function () {
        Route::get('/ai-chat', function () {
            return view('ai-chat');
        })->name('ai.chat.view');
    });

    Route::post('/auth/logout', [PhoneAuthController::class, 'logout'])->name('auth.logout');

    // Web Wallet Recharge
    Route::get('/wallet/recharge', [App\Http\Controllers\WalletController::class, 'showRecharge'])->name('wallet.recharge');
    Route::post('/wallet/recharge', [\App\Http\Controllers\PaymentController::class, 'initiateWeb'])->name('wallet.recharge.init');

    // Astrologer Only
    Route::middleware(['role:Astrologer'])->prefix('astrologer')->name('astrologer.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AstrologerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\AstrologerDashboardController::class, 'editProfile'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\AstrologerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/services', [App\Http\Controllers\AstrologerDashboardController::class, 'editServices'])->name('services');
        Route::post('/services', [App\Http\Controllers\AstrologerDashboardController::class, 'updateServices'])->name('services.update');
        Route::get('/availability', [App\Http\Controllers\AstrologerDashboardController::class, 'editAvailability'])->name('availability');
        Route::post('/availability', [App\Http\Controllers\AstrologerDashboardController::class, 'updateAvailability'])->name('availability.update');
        Route::get('/calls', [App\Http\Controllers\AstrologerDashboardController::class, 'calls'])->name('calls');
        Route::get('/chats', [App\Http\Controllers\ChatController::class, 'astrologerIndex'])->name('chats');
    });
});

Route::get('/astrologers', [App\Http\Controllers\AstrologerDirectoryController::class, 'index'])->name('astrologers.index');
Route::get('/astrologers/{id}', [App\Http\Controllers\AstrologerDirectoryController::class, 'show'])->name('astrologers.public_show');
Route::post('/api/astrologers/{id}/gate/{type}', [App\Http\Controllers\AstrologerDirectoryController::class, 'gate'])->name('astrologers.gate');

Route::post('/webhooks/callerdesk', [\App\Http\Controllers\WebhookController::class, 'handleCallerDesk'])->name('webhooks.callerdesk');

Route::get('/firebase/health', [FirebaseExampleController::class, 'health'])->name('firebase.health');

require __DIR__ . '/admin.php';
