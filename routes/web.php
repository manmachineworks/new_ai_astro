<?php

use App\Http\Controllers\PhoneAuthController;
use App\Http\Controllers\FirebaseExampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::view('/offline', 'offline')->name('offline');
Route::get('/auth/login', function () {
    return redirect()->route('auth.phone.show');
})->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [PhoneAuthController::class, 'show'])->name('login');
    Route::get('/auth/phone', [PhoneAuthController::class, 'show'])->name('auth.phone.show');
    Route::post('/auth/phone/verify', [PhoneAuthController::class, 'verify'])->name('auth.phone.verify');
    Route::post('/auth/email/login', [PhoneAuthController::class, 'loginWithEmail'])->name('auth.email.login');
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
    Route::post('/devices/revoke-token', [App\Http\Controllers\DeviceController::class, 'revokeToken'])->name('devices.revoke');

    // AI Chat
    Route::get('/ai-chat', [App\Http\Controllers\AiChatController::class, 'index'])->name('user.ai_chat.index');
    Route::post('/ai-chat/start', [App\Http\Controllers\AiChatController::class, 'start'])->name('user.ai_chat.start');
    Route::get('/ai-chat/{session}', [App\Http\Controllers\AiChatController::class, 'show'])->name('user.ai_chat.show');
    Route::post('/ai-chat/{session}/message', [App\Http\Controllers\AiChatController::class, 'sendMessage'])->name('user.ai_chat.send');
    Route::post('/ai-chat/message/{id}/report', [App\Http\Controllers\AiChatController::class, 'reportMessage'])->name('user.ai_chat.report');

    // Horoscopes
    Route::get('/horoscope', [App\Http\Controllers\HoroscopeController::class, 'index'])->name('user.horoscope.index');
    Route::get('/horoscope/daily', [App\Http\Controllers\HoroscopeController::class, 'daily'])->name('user.horoscope.daily');
    Route::get('/horoscope/weekly', [App\Http\Controllers\HoroscopeController::class, 'weekly'])->name('user.horoscope.weekly');
    Route::get('/kundli', [App\Http\Controllers\HoroscopeController::class, 'kundliForm'])->name('user.kundli.form');
    Route::post('/kundli', [App\Http\Controllers\HoroscopeController::class, 'getKundli'])->name('user.kundli.get');

    Route::post('/auth/logout', [PhoneAuthController::class, 'logout'])->name('auth.logout');

    // Web Wallet Recharge
    Route::get('/wallet/recharge', [\App\Http\Controllers\PaymentController::class, 'showRecharge'])->name('wallet.show');
    Route::post('/wallet/recharge', [\App\Http\Controllers\PaymentController::class, 'initiateRecharge'])->name('wallet.initiate');

    // Astrologer Only
    Route::middleware(['role:Astrologer'])->prefix('astrologer')->name('astrologer.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AstrologerDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/layout', [\App\Http\Controllers\AstrologerDashboardController::class, 'updateDashboardLayout'])->name('dashboard.layout');
        Route::get('/profile', [App\Http\Controllers\AstrologerDashboardController::class, 'editProfile'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\AstrologerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/toggle-status', [\App\Http\Controllers\AstrologerDashboardController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/services', [App\Http\Controllers\AstrologerDashboardController::class, 'editServices'])->name('services');
        Route::post('/services', [App\Http\Controllers\AstrologerDashboardController::class, 'updateServices'])->name('services.update');
        Route::get('/availability', [App\Http\Controllers\AstrologerDashboardController::class, 'editAvailability'])->name('availability');
        Route::post('/availability', [App\Http\Controllers\AstrologerDashboardController::class, 'updateAvailability'])->name('availability.update');
        Route::get('/calls', [App\Http\Controllers\AstrologerDashboardController::class, 'calls'])->name('calls');
        Route::get('/earnings', [App\Http\Controllers\AstrologerDashboardController::class, 'earnings'])->name('earnings');
        Route::get('/chats', [App\Http\Controllers\ChatController::class, 'astrologerIndex'])->name('chats');

        // Astrologer Appointments
        Route::get('/appointments', [App\Http\Controllers\AstrologerAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments/{id}/confirm', [App\Http\Controllers\AstrologerAppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('/appointments/{id}/decline', [App\Http\Controllers\AstrologerAppointmentController::class, 'decline'])->name('appointments.decline');
        Route::post('/appointments/{id}/cancel', [App\Http\Controllers\AstrologerAppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::get('/slots', [App\Http\Controllers\AstrologerAppointmentController::class, 'slots'])->name('slots.index');
        Route::post('/slots/{id}/block', [App\Http\Controllers\AstrologerAppointmentController::class, 'blockSlot'])->name('slots.block');
        Route::post('/slots/{id}/unblock', [App\Http\Controllers\AstrologerAppointmentController::class, 'unblockSlot'])->name('slots.unblock');
    });

    // User Appointments (authenticated users)
    Route::get('/appointments', [App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{id}', [App\Http\Controllers\AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/hold', [App\Http\Controllers\AppointmentController::class, 'hold'])->name('appointments.hold');
    Route::post('/appointments', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{id}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');

    Route::post('/withdrawals', [App\Http\Controllers\WithdrawalController::class, 'store'])
        ->middleware(['role:Astrologer'])
        ->name('withdrawals.store');

    // Promo & Referral Routes
    Route::prefix('api')->group(function () {
        Route::post('/promos/validate', [App\Http\Controllers\PromoController::class, 'validate'])->name('promos.validate');
        Route::get('/promos/first-time-eligible', [App\Http\Controllers\PromoController::class, 'checkFirstTimeEligible'])->name('promos.first_time');
        Route::get('/referrals/code', [App\Http\Controllers\PromoController::class, 'getReferralCode'])->name('referrals.code');
        Route::get('/referrals/stats', [App\Http\Controllers\PromoController::class, 'getReferralStats'])->name('referrals.stats');
    });

    // Support Tickets
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\SupportTicketController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\SupportTicketController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{id}/messages', [App\Http\Controllers\SupportTicketController::class, 'addMessage'])->name('add_message');
    });

    // Disputes
    Route::prefix('disputes')->name('disputes.')->group(function () {
        Route::get('/', [App\Http\Controllers\DisputeController::class, 'index'])->name('index');
        Route::get('/check-eligibility', [App\Http\Controllers\DisputeController::class, 'checkEligibility'])->name('check_eligibility');
        Route::post('/', [App\Http\Controllers\DisputeController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\DisputeController::class, 'show'])->name('show');
    });
});

// Public appointment slot listing
Route::get('/astrologers/{id}/appointment-slots', [App\Http\Controllers\AppointmentController::class, 'listSlots'])->name('appointments.list_slots');

Route::get('/astrologers', [App\Http\Controllers\AstrologerDirectoryController::class, 'index'])->name('astrologers.index');
Route::get('/astrologers/{id}', [App\Http\Controllers\AstrologerDirectoryController::class, 'show'])->name('astrologers.public_show');
Route::post('/api/astrologers/{id}/gate/{type}', [App\Http\Controllers\AstrologerDirectoryController::class, 'gate'])->name('astrologers.gate');

Route::post('/webhooks/callerdesk', [\App\Http\Controllers\WebhookController::class, 'handleCallerDesk'])->name('webhooks.callerdesk');

Route::get('/firebase/health', [FirebaseExampleController::class, 'health'])->name('firebase.health');

Route::get('/health', [App\Http\Controllers\HealthController::class, 'index'])->name('health');
Route::get('/health/db', [App\Http\Controllers\HealthController::class, 'database'])->name('health.db');
Route::get('/health/queue', [App\Http\Controllers\HealthController::class, 'queue'])->name('health.queue');

// Language Switching
Route::post('/locale/switch', [App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

// CMS Pages
Route::get('/pages/{slug}', [App\Http\Controllers\CmsController::class, 'show'])->name('cms.page');


// Public Blog
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{slug}', [App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// User Preferences
Route::middleware('auth')->group(function () {
    Route::get('/preferences', [App\Http\Controllers\UserPreferenceController::class, 'edit'])->name('user.preferences.edit');
    Route::post('/preferences', [App\Http\Controllers\UserPreferenceController::class, 'update'])->name('user.preferences.update');
});

// Search & Ask
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search'])->name('api.search');
Route::post('/api/ask', [App\Http\Controllers\SearchController::class, 'ask'])->name('api.ask');

// Memberships
Route::middleware('auth')->group(function () {
    Route::get('/memberships', [App\Http\Controllers\MembershipController::class, 'index'])->name('memberships.index');
    Route::get('/user/membership', [App\Http\Controllers\MembershipController::class, 'myMembership'])->name('memberships.my');
    Route::post('/memberships/checkout/{plan}', [App\Http\Controllers\MembershipController::class, 'checkout'])->name('memberships.checkout');
});

// Deep Linking
Route::get('/dl/{type}/{id}', [App\Http\Controllers\DeepLinkController::class, 'handle'])->name('deeplink');

require __DIR__ . '/admin.php';
