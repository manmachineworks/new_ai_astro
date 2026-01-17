<?php

namespace App\Jobs;

use App\Models\ChatSession;
use App\Models\User;
use App\Services\FCMNotificationService;
use App\Services\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChargeActiveChatSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(WalletService $walletService, FCMNotificationService $fcmService): void
    {
        // 1. Fetch active sessions
        $activeSessions = ChatSession::where('status', 'active')->get();

        foreach ($activeSessions as $session) {
            // ... (Billing Logic Omitted for Brevity) ...

            // SIMULATION: Check Balance
            $user = $session->user;
            $balance = $user->wallet_balance;
            $costPerMessage = 5.00; // Example

            if ($balance < $costPerMessage) {
                // 2. Lock Session
                $session->update(['status' => 'locked']);

                // 3. Notify User: Wallet Exhausted
                $fcmService->sendToUser(
                    $user,
                    'wallet_exhausted',
                    [
                        'balance' => (string) $balance,
                        'session_id' => (string) $session->id,
                        'deeplink' => 'app://wallet/recharge'
                    ],
                    'Recharge to Continue',
                    "Your balance is too low to send messages. Please recharge."
                );

                // 4. Notify Astrologer: Chat Locked
                $astrologer = $session->astrologer->user; // Assuming relation
                if ($astrologer) {
                    $fcmService->sendToUser(
                        $astrologer,
                        'chat_session_locked',
                        [
                            'session_id' => (string) $session->id,
                            'user_label' => $fcmService->safeMaskedLabel($user)
                        ],
                        'Chat Paused',
                        "Chat with {$fcmService->safeMaskedLabel($user)} is paused due to low balance."
                    );
                }

                Log::info("Chat Session {$session->id} locked due to low balance.");
            }
        }
    }
}
