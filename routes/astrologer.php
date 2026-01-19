<?php

use App\Http\Controllers\Astrologer\AppointmentController;
use App\Http\Controllers\Astrologer\CallLogController;
use App\Http\Controllers\Astrologer\ChatSessionController;
use App\Http\Controllers\Astrologer\EarningsController;
use App\Http\Controllers\Astrologer\OverviewController;
use App\Http\Controllers\Astrologer\PricingController;
use App\Http\Controllers\Astrologer\ProfileController;
use App\Http\Controllers\Astrologer\ScheduleController;
use App\Http\Controllers\Astrologer\ServiceController;
use App\Http\Controllers\Astrologer\TimeOffController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Astrologer'])->prefix('astrologer')->name('astrologer.')->group(function () {
    Route::get('/', fn () => redirect()->route('astrologer.overview'))->name('root');

    Route::get('/overview', [OverviewController::class, 'index'])->name('overview');
    Route::get('/services', [ServiceController::class, 'edit'])->name('services');
    Route::put('/services', [ServiceController::class, 'update'])->name('services.update');
    Route::get('/pricing', [PricingController::class, 'edit'])->name('pricing');
    Route::put('/pricing', [PricingController::class, 'update'])->name('pricing.update');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::put('/schedule/{schedule}', [ScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('/schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::post('/time-off', [TimeOffController::class, 'store'])->name('timeoff.store');
    Route::delete('/time-off/{timeOff}', [TimeOffController::class, 'destroy'])->name('timeoff.destroy');

    Route::get('/calls', [CallLogController::class, 'index'])->name('calls.index');
    Route::get('/calls/{call}', [CallLogController::class, 'show'])->name('calls.show');

    Route::get('/chats', [ChatSessionController::class, 'index'])->name('chats.index');
    Route::get('/chats/{session}', [ChatSessionController::class, 'show'])->name('chats.show');
    Route::post('/chats/{session}/close', [ChatSessionController::class, 'close'])->name('chats.close');
    Route::post('/chats/{session}/block', [ChatSessionController::class, 'block'])->name('chats.block');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])->name('appointments.accept');
    Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::post('/appointments/{appointment}/notes', [AppointmentController::class, 'updateNotes'])->name('appointments.notes');

    Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/verification', [ProfileController::class, 'uploadVerification'])->name('profile.verification');
});
