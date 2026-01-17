<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ReferralCode;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-generate referral code for new user
        try {
            ReferralCode::createForUser($user);
        } catch (\Exception $e) {
            \Log::error('Failed to create referral code for user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
