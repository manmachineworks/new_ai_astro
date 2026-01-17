<?php

namespace App\Console\Commands;

use App\Models\DailyMetric;
use App\Models\User;
use App\Models\CallSession;
use App\Models\ChatSession;
use App\Models\ChatMessageCharge;
use App\Models\AiChatSession;
use App\Models\AiMessageCharge;
use App\Models\PaymentOrder;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComputeDailyMetrics extends Command
{
    protected $signature = 'metrics:daily {date? : Date in YYYY-MM-DD format (IST)}';
    protected $description = 'Compute and store daily marketplace metrics';

    public function handle()
    {
        $dateStr = $this->argument('date') ?: Carbon::now('Asia/Kolkata')->toDateString();
        $date = Carbon::parse($dateStr, 'Asia/Kolkata');

        $startUtc = $date->copy()->startOfDay()->setTimezone('UTC');
        $endUtc = $date->copy()->endOfDay()->setTimezone('UTC');

        $this->info("Computing metrics for {$dateStr} (IST)");
        $this->comment("Range UTC: {$startUtc} to {$endUtc}");

        // 1. Calls
        $calls = CallSession::where('status', 'completed')
            ->whereBetween('updated_at', [$startUtc, $endUtc])
            ->selectRaw('SUM(gross_amount) as gross, SUM(platform_commission_amount) as commission')
            ->first();
        $callGross = $calls->gross ?: 0;
        $callComm = $calls->commission ?: 0;
        $callEarn = $callGross - $callComm;

        // 2. Human Chat (from charges)
        $chatGross = ChatMessageCharge::whereBetween('created_at', [$startUtc, $endUtc])
            ->sum('amount');
        // For human chat, commission is usually percentage. Let's assume sessions store aggregated if we updated it, 
        // but for now let's derive from chat_sessions updated in this range if they are settled.
        // Better: Use chat_sessions settled in this range.
        $chatSessions = ChatSession::where('status', 'completed')
            ->whereBetween('updated_at', [$startUtc, $endUtc])
            ->selectRaw('SUM(total_charged) as gross, SUM(commission_amount_total) as commission')
            ->first();
        $chatGrossFinal = $chatSessions->gross ?: 0;
        $chatCommFinal = $chatSessions->commission ?: 0;
        $chatEarnFinal = $chatGrossFinal - $chatCommFinal;

        // 3. AI Chat
        $aiSessions = AiChatSession::whereBetween('updated_at', [$startUtc, $endUtc])
            ->selectRaw('SUM(total_charged) as gross, SUM(commission_amount_total) as commission')
            ->first();
        $aiGross = $aiSessions->gross ?: 0;
        $aiComm = $aiSessions->commission ?: 0;
        $aiEarn = $aiGross - $aiComm;

        // 4. Wallet Recharges
        $rechargesSuccess = PaymentOrder::where('status', 'PAID')
            ->whereBetween('updated_at', [$startUtc, $endUtc])
            ->sum('amount');
        $rechargeCountSuccess = PaymentOrder::where('status', 'PAID')
            ->whereBetween('updated_at', [$startUtc, $endUtc])
            ->count();
        $rechargeCountFailed = PaymentOrder::whereIn('status', ['FAILED', 'EXPIRED'])
            ->whereBetween('updated_at', [$startUtc, $endUtc])
            ->count();

        // 5. User Stats
        $newUsers = User::whereBetween('created_at', [$startUtc, $endUtc])->count();

        // Active users: Users who performed any transaction or session
        $activeUsers = DB::table('wallet_transactions')
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->distinct('user_id')
            ->count('user_id');

        // Upsert
        DailyMetric::updateOrCreate(
            ['date_ist' => $dateStr],
            [
                'call_gross' => $callGross,
                'call_commission' => $callComm,
                'call_earnings' => $callEarn,
                'chat_gross' => $chatGrossFinal,
                'chat_commission' => $chatCommFinal,
                'chat_earnings' => $chatEarnFinal,
                'ai_gross' => $aiGross,
                'ai_commission' => $aiComm,
                'ai_earnings' => $aiEarn,
                'wallet_recharge_success' => $rechargesSuccess,
                'wallet_recharge_count_success' => $rechargeCountSuccess,
                'wallet_recharge_count_failed' => $rechargeCountFailed,
                'new_users' => $newUsers,
                'active_users' => $activeUsers,
            ]
        );

        $this->info("Metrics stored successfully for {$dateStr}");
    }
}
