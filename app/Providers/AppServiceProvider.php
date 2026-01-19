<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\CallLog;
use App\Models\ChatSession;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use App\Policies\AstrologerPolicy;
use App\Policies\CallLogPolicy;
use App\Policies\ChatSessionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Astrologer::class, AstrologerPolicy::class);
        Gate::policy(CallLog::class, CallLogPolicy::class);
        Gate::policy(ChatSession::class, ChatSessionPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);

        // Register observers
        User::observe(\App\Observers\UserObserver::class);
    }
}
