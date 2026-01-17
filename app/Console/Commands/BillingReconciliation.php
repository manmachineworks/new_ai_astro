<?php

namespace App\Console\Commands;

use App\Models\CallSession;
use App\Models\WalletHold;
use App\Services\WalletService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillingReconciliation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:reconcile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release stuck wallet holds and mark stale sessions as failed';

    /**
     * Execute the console command.
     */
    public function handle(WalletService $walletService)
    {
        $this->info('Starting Billing Reconciliation...');

        // 1. Release Expired Holds
        $expiredHolds = WalletHold::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredHolds as $hold) {
            try {
                $this->info("Releasing Hold #{$hold->id}");
                $walletService->releaseHold($hold);
            } catch (\Exception $e) {
                Log::error("Failed to release hold #{$hold->id}: " . $e->getMessage());
            }
        }

        // 2. Mark Stale Call Sessions
        $staleCalls = CallSession::whereIn('status', ['initiated', 'connecting'])
            ->where('created_at', '<', now()->subHour())
            ->get();

        foreach ($staleCalls as $call) {
            $this->info("Marking Call #{$call->id} as failed");
            $call->update(['status' => 'failed', 'meta' => array_merge($call->meta ?? [], ['reason' => 'stale_timeout'])]);

            // Release linked hold if any
            if (isset($call->meta['wallet_hold_id'])) {
                $hold = WalletHold::find($call->meta['wallet_hold_id']);
                if ($hold && $hold->status === 'active') {
                    $walletService->releaseHold($hold);
                }
            }
        }

        $this->info('Reconciliation Completed.');
    }
}
