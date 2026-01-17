<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationDispatcher;
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
    public function handle(NotificationDispatcher $dispatcher): void
    {
        // Threshold: 50 INR
        $threshold = 50.00;

        User::role('User')->where('wallet_balance', '<', $threshold)
            ->where('is_active', true)
            ->chunk(100, function ($users) use ($dispatcher) {
                foreach ($users as $user) {
                    $dispatcher->dispatch(
                        'wallet_low',
                        $user,
                        [
                            'balance' => (string) $user->wallet_balance,
                            'currency' => 'INR'
                        ],
                        [
                            'throttle_key' => "throttle:wallet_low:{$user->id}",
                            'throttle_ttl' => 86400, // 24 hours
                            'priority' => 'high',
                            'deeplink' => 'app://wallet/recharge'
                        ]
                    );
                }
            });
    }

    protected function checkAndAlert(User $user)
    {
        // Method deprecated by Dispatcher usage in handle()
    }
}
