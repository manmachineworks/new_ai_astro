<?php

namespace App\Jobs;

use App\Models\ChatSession;
use App\Services\WalletService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(WalletService $walletService): void
    {
        // Find active sessions that need billing
        $sessions = ChatSession::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('pricing_mode')
                    ->orWhere('pricing_mode', 'per_minute');
            })
            ->where(function ($query) {
                $query->where(function ($q) {
                    // First minute billing: if started > 1 min ago and never billed
                    $q->whereNull('last_billed_at')
                        ->where('started_at', '<=', now()->subMinute());
                })->orWhere(function ($q) {
                    // Subsequent billing: if last billed > 1 min ago
                    $q->whereNotNull('last_billed_at')
                        ->where('last_billed_at', '<=', now()->subMinute());
                });
            })
            ->get();

        foreach ($sessions as $session) {
            try {
                $rate = $session->rate_per_minute;

                // Debit User
                $walletService->debit(
                    $session->user,
                    $rate,
                    'chat',
                    $session->id,
                    'Chat charge for ' . now()->toTimeString()
                );

                // Credit Astrologer (70%)
                $earning = $rate * 0.70;
                $walletService->credit(
                    $session->astrologer,
                    $earning,
                    'chat_earning',
                    $session->id,
                    'Chat earning'
                );

                // Update Session
                $session->update([
                    'cost' => $session->cost + $rate,
                    'duration_minutes' => $session->duration_minutes + 1,
                    'last_billed_at' => now(),
                    'updated_at' => now(), // bump updated_at so we don't bill again immediately if job runs fast
                ]);

            } catch (Exception $e) {
                // Insufficient Balance or Error -> End Chat
                Log::warning("Ending chat {$session->id} due to payment failure: " . $e->getMessage());

                $session->update(['status' => 'completed', 'ended_at' => now()]);

                // Notify Firebase
                app(\App\Services\ChatService::class)->endFirebaseConversation($session->firebase_chat_id ?? 'mock_id');
            }
        }
    }
}
