<?php

namespace App\Console\Commands;

use App\Models\DailyMetric;
use App\Models\User;
use App\Models\CallSession;
use App\Models\ChatMessageCharge;
use App\Models\AiMessageCharge;
use App\Models\PaymentOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        // 1. Calls (settled)
        $callDateColumn = Schema::hasColumn('call_sessions', 'settled_at')
            ? 'settled_at'
            : (Schema::hasColumn('call_sessions', 'ended_at_utc') ? 'ended_at_utc' : (Schema::hasColumn('call_sessions', 'ended_at') ? 'ended_at' : 'updated_at'));
        $callGrossColumn = Schema::hasColumn('call_sessions', 'gross_amount') ? 'gross_amount' : 'cost';
        $callCommissionColumn = Schema::hasColumn('call_sessions', 'platform_commission_amount') ? 'platform_commission_amount' : null;
        $callEarningsColumn = Schema::hasColumn('call_sessions', 'astrologer_earnings_amount') ? 'astrologer_earnings_amount' : null;

        $calls = CallSession::query()
            ->when(Schema::hasColumn('call_sessions', 'status'), function ($query) {
                $query->where('status', 'completed');
            })
            ->whereBetween($callDateColumn, [$startUtc, $endUtc])
            ->selectRaw("SUM({$callGrossColumn}) as gross" . ($callCommissionColumn ? ", SUM({$callCommissionColumn}) as commission" : ''))
            ->first();
        $callGross = (float) ($calls->gross ?? 0);
        $callComm = $callCommissionColumn ? (float) ($calls->commission ?? 0) : 0.0;
        $callEarn = $callGross - $callComm;
        if ($callEarningsColumn) {
            $callEarn = (float) CallSession::whereBetween($callDateColumn, [$startUtc, $endUtc])->sum($callEarningsColumn);
            $callComm = $callGross - $callEarn;
        }

        // 2. Human Chat (ledger charges)
        $chatGross = (float) ChatMessageCharge::whereBetween('created_at', [$startUtc, $endUtc])
            ->sum('amount');
        $chatComm = 0.0;
        if (Schema::hasColumn('chat_sessions', 'commission_percent_snapshot')) {
            $chatComm = (float) DB::table('chat_message_charges')
                ->join('chat_sessions', 'chat_sessions.id', '=', 'chat_message_charges.chat_session_id')
                ->whereBetween('chat_message_charges.created_at', [$startUtc, $endUtc])
                ->selectRaw('SUM(chat_message_charges.amount * (COALESCE(chat_sessions.commission_percent_snapshot, 0) / 100)) as commission')
                ->value('commission');
        }
        $chatEarnFinal = $chatGross - $chatComm;

        // 3. AI Chat (ledger charges)
        $aiGross = (float) AiMessageCharge::whereBetween('created_at', [$startUtc, $endUtc])
            ->sum('amount');
        $aiComm = 0.0;
        if (Schema::hasColumn('ai_chat_sessions', 'commission_percent_snapshot')) {
            $aiComm = (float) DB::table('ai_message_charges')
                ->join('ai_chat_sessions', 'ai_chat_sessions.id', '=', 'ai_message_charges.ai_chat_session_id')
                ->whereBetween('ai_message_charges.created_at', [$startUtc, $endUtc])
                ->selectRaw('SUM(ai_message_charges.amount * (COALESCE(ai_chat_sessions.commission_percent_snapshot, 0) / 100)) as commission')
                ->value('commission');
        }
        $aiEarn = $aiGross - $aiComm;

        // 4. Wallet Recharges (PhonePe orders)
        $successStatuses = ['success', 'PAID'];
        $failedStatuses = ['failed', 'FAILED', 'EXPIRED', 'expired'];
        $rechargeBase = PaymentOrder::query()
            ->when(Schema::hasColumn('payment_orders', 'type'), function ($query) {
                $query->where('type', 'wallet_recharge');
            })
            ->whereBetween('updated_at', [$startUtc, $endUtc]);
        $rechargesSuccess = (clone $rechargeBase)->whereIn('status', $successStatuses)->sum('amount');
        $rechargeCountSuccess = (clone $rechargeBase)->whereIn('status', $successStatuses)->count();
        $rechargeCountFailed = (clone $rechargeBase)->whereIn('status', $failedStatuses)->count();

        // 5. Refunds
        $refundsAmount = 0.0;
        if (Schema::hasTable('refunds')) {
            $refundsAmount = (float) DB::table('refunds')
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$startUtc, $endUtc])
                ->sum('amount');
        }

        // 6. User Stats
        $newUsers = User::whereBetween('created_at', [$startUtc, $endUtc])->count();

        // Active users: Users who performed any transaction or session (ledger-first)
        $walletActivity = DB::table('wallet_transactions')
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->select('user_id');
        $callActivity = DB::table('call_sessions')
            ->whereBetween($callDateColumn, [$startUtc, $endUtc])
            ->select('user_id');
        $chatActivity = DB::table('chat_message_charges')
            ->join('chat_sessions', 'chat_sessions.id', '=', 'chat_message_charges.chat_session_id')
            ->whereBetween('chat_message_charges.created_at', [$startUtc, $endUtc])
            ->select('chat_sessions.user_id as user_id');
        $aiActivity = DB::table('ai_message_charges')
            ->join('ai_chat_sessions', 'ai_chat_sessions.id', '=', 'ai_message_charges.ai_chat_session_id')
            ->whereBetween('ai_message_charges.created_at', [$startUtc, $endUtc])
            ->select('ai_chat_sessions.user_id as user_id');

        $activeUsers = DB::query()
            ->fromSub(
                $walletActivity->union($callActivity)->union($chatActivity)->union($aiActivity),
                'activity'
            )
            ->distinct()
            ->count('user_id');

        // Upsert
        DailyMetric::updateOrCreate(
            ['date_ist' => $dateStr],
            [
                'call_gross' => $callGross,
                'call_commission' => $callComm,
                'call_earnings' => $callEarn,
                'chat_gross' => $chatGross,
                'chat_commission' => $chatComm,
                'chat_earnings' => $chatEarnFinal,
                'ai_gross' => $aiGross,
                'ai_commission' => $aiComm,
                'ai_earnings' => $aiEarn,
                'wallet_recharge_success' => $rechargesSuccess,
                'wallet_recharge_count_success' => $rechargeCountSuccess,
                'wallet_recharge_count_failed' => $rechargeCountFailed,
                'refunds_amount' => $refundsAmount,
                'new_users' => $newUsers,
                'active_users' => $activeUsers,
            ]
        );

        $this->info("Metrics stored successfully for {$dateStr}");
    }
}
