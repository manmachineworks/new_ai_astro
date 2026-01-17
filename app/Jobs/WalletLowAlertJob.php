<?php

namespace App\Jobs;

use App\Models\User;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class WalletLowAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Threshold: 50 INR
        $threshold = 50.00;

        User::role('User')->where('wallet_balance', '<', $threshold)
            ->where('is_active', true)
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->checkAndAlert($user);
                }
            });
    }

    protected function checkAndAlert(User $user)
    {
        // Cache Key: wallet_low_alert_{userId}
        // Throttling: Alert max once per 24 hours
        $cacheKey = "wallet_low_alert_{$user->id}";

        if (Cache::has($cacheKey)) {
            return; // Already alerted recently
        }

        // Logic: Only alert if they haven't recharged recently? 
        // Or strictly if balance is low.
        // Assuming strict low balance check for now.

        SendPushNotificationJob::dispatch(
            $user->id,
            'wallet_low',
            [
                'balance' => (string) $user->wallet_balance,
                'currency' => 'INR'
            ],
            'Low Balance Warning',
            "Your balance is low (INR {$user->wallet_balance}). Top up to continue chats.",
            'app://wallet/recharge'
        );

        // Set Cache for 24 hours
        Cache::put($cacheKey, true, now()->addHours(24));
    }
}
