<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyMetric;
use App\Models\CallSession;
use App\Models\ChatSession;
use App\Models\AiChatSession;
use App\Models\PaymentOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ReportingController extends Controller
{
    public function dashboard(Request $request)
    {
        $range = $this->getRange($request);
        $startDate = $range['start_ist']->toDateString();
        $endDate = $range['end_ist']->toDateString();
        $cacheKey = "admin_report_overview_{$startDate}_{$endDate}";

        $data = Cache::remember($cacheKey, 600, function () use ($range, $startDate, $endDate) {
            $metrics = DailyMetric::whereBetween('date_ist', [$startDate, $endDate])
                ->selectRaw('
                    SUM(call_gross) as call_gross,
                    SUM(call_commission) as call_commission,
                    SUM(call_earnings) as call_earnings,
                    SUM(chat_gross) as chat_gross,
                    SUM(chat_commission) as chat_commission,
                    SUM(chat_earnings) as chat_earnings,
                    SUM(ai_gross) as ai_gross,
                    SUM(ai_commission) as ai_commission,
                    SUM(ai_earnings) as ai_earnings,
                    SUM(wallet_recharge_success) as recharge_total,
                    SUM(wallet_recharge_count_success) as recharge_count_ok,
                    SUM(wallet_recharge_count_failed) as recharge_count_fail,
                    SUM(refunds_amount) as refunds_amount
                ')
                ->first();

            $totalGross = ($metrics->call_gross ?? 0) + ($metrics->chat_gross ?? 0) + ($metrics->ai_gross ?? 0);
            $totalComm = ($metrics->call_commission ?? 0) + ($metrics->chat_commission ?? 0) + ($metrics->ai_commission ?? 0);
            $totalEarn = ($metrics->call_earnings ?? 0) + ($metrics->chat_earnings ?? 0) + ($metrics->ai_earnings ?? 0);

            $rechargeTotal = $metrics->recharge_total ?? 0;
            $rechargeOk = $metrics->recharge_count_ok ?? 0;
            $rechargeFail = $metrics->recharge_count_fail ?? 0;
            $rechargeRate = ($rechargeOk + $rechargeFail) > 0 ? ($rechargeOk / ($rechargeOk + $rechargeFail)) * 100 : 0;

            $refundsTotal = $metrics->refunds_amount ?? 0;

            $newUsers = User::whereBetween('created_at', [$range['start_utc'], $range['end_utc']])->count();
            $activeUsers = $this->countActiveUsers($range['start_utc'], $range['end_utc']);

            $revenueTrend = DailyMetric::whereBetween('date_ist', [$startDate, $endDate])
                ->orderBy('date_ist')
                ->get(['date_ist', 'call_gross', 'chat_gross', 'ai_gross']);

            $rechargeTrend = DailyMetric::whereBetween('date_ist', [$startDate, $endDate])
                ->orderBy('date_ist')
                ->get(['date_ist', 'wallet_recharge_count_success', 'wallet_recharge_count_failed']);

            $topAstrologers = DB::table('astrologer_earnings_ledger')
                ->join('astrologer_profiles', 'astrologer_profiles.id', '=', 'astrologer_earnings_ledger.astrologer_profile_id')
                ->join('users', 'users.id', '=', 'astrologer_profiles.user_id')
                ->whereBetween('astrologer_earnings_ledger.created_at', [$range['start_utc'], $range['end_utc']])
                ->whereIn('astrologer_earnings_ledger.status', ['available', 'paid'])
                ->selectRaw('astrologer_profiles.id as profile_id, users.name as astrologer_name, users.email as astrologer_email, SUM(astrologer_earnings_ledger.amount) as total_earnings')
                ->groupBy('astrologer_profiles.id', 'users.name', 'users.email')
                ->orderByDesc('total_earnings')
                ->limit(10)
                ->get();

            return compact(
                'totalGross',
                'totalComm',
                'totalEarn',
                'rechargeTotal',
                'rechargeRate',
                'refundsTotal',
                'newUsers',
                'activeUsers',
                'revenueTrend',
                'rechargeTrend',
                'topAstrologers'
            );
        });

        return view('admin.reports.dashboard', array_merge($data, ['range' => $range]));
    }

    public function revenue(Request $request)
    {
        $range = $this->getRange($request);
        if ($request->boolean('export')) {
            return $this->exportRevenueSummary($range);
        }

        $startDate = $range['start_ist']->toDateString();
        $endDate = $range['end_ist']->toDateString();

        $metricsQuery = DailyMetric::whereBetween('date_ist', [$startDate, $endDate])
            ->orderByDesc('date_ist');

        $totals = DailyMetric::whereBetween('date_ist', [$startDate, $endDate])
            ->selectRaw('
                SUM(call_gross) as call_gross,
                SUM(chat_gross) as chat_gross,
                SUM(ai_gross) as ai_gross,
                SUM(call_commission) as call_commission,
                SUM(chat_commission) as chat_commission,
                SUM(ai_commission) as ai_commission
            ')
            ->first();

        $metrics = $metricsQuery->paginate(20)->withQueryString();

        return view('admin.reports.revenue', compact('range', 'metrics', 'totals'));
    }

    public function revenueItems(Request $request)
    {
        $range = $this->getRange($request);
        $type = $request->get('type', 'call');
        if (!in_array($type, ['call', 'chat', 'ai'], true)) {
            $type = 'call';
        }

        if ($request->boolean('export')) {
            return $this->exportRevenueItems($range, $type);
        }

        $itemsQuery = $this->buildRevenueItemsQuery($type, $range);
        $items = $itemsQuery->orderByDesc('occurred_at')->paginate(25)->withQueryString();

        return view('admin.reports.revenue_items', compact('range', 'items', 'type'));
    }

    public function export(Request $request)
    {
        $range = $this->getRange($request);
        $report = $request->get('report');

        return match ($report) {
            'revenue-summary' => $this->exportRevenueSummary($range),
            'revenue-items' => $this->exportRevenueItems($range, $request->get('type', 'call')),
            default => abort(404),
        };
    }

    public function refunds(Request $request)
    {
        $range = $this->getRange($request);
        $query = DB::table('refunds')
            ->leftJoin('users', 'users.id', '=', 'refunds.user_id')
            ->whereBetween('refunds.updated_at', [$range['start_utc'], $range['end_utc']])
            ->select([
                'refunds.id',
                'refunds.reference_type',
                'refunds.reference_id',
                'refunds.amount',
                'refunds.status',
                'refunds.reason',
                'refunds.updated_at',
                'users.name as user_name',
            ])
            ->orderByDesc('refunds.updated_at');

        if ($request->filled('status')) {
            $query->where('refunds.status', $request->status);
        }

        if ($request->boolean('export')) {
            $cols = [
                'id' => 'Refund ID',
                'user_name' => 'User',
                'amount' => 'Amount',
                'status' => 'Status',
                'reason' => 'Reason',
                'reference_type' => 'Reference Type',
                'reference_id' => 'Reference ID',
                'updated_at' => 'Date',
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('refunds.csv', $query, $cols);
        }

        $refunds = $query->paginate(20)->withQueryString();
        return view('admin.reports.refunds', compact('refunds', 'range'));
    }

    public function recharges(Request $request)
    {
        $range = $this->getRange($request);
        $query = PaymentOrder::with('user')
            ->when(Schema::hasColumn('payment_orders', 'type'), function ($query) {
                $query->where('type', 'wallet_recharge');
            })
            ->whereBetween('updated_at', [$range['start_utc'], $range['end_utc']])
            ->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('export')) {
            $cols = [
                'merchant_transaction_id' => 'Transaction ID',
                'user.name' => 'User',
                'amount' => 'Amount',
                'status' => 'Status',
                'updated_at' => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('recharges.csv', $query, $cols);
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.reports.recharges', compact('orders', 'range'));
    }

    public function calls(Request $request)
    {
        $range = $this->getRange($request);
        $callDateColumn = $this->callDateColumn();
        $query = CallSession::with(['user', 'astrologerProfile.user'])
            ->whereBetween($callDateColumn, [$range['start_utc'], $range['end_utc']])
            ->latest($callDateColumn);

        if ($request->has('export')) {
            $cols = [
                'id' => 'Session ID',
                'user.name' => 'User',
                'astrologerProfile.user.name' => 'Astrologer',
                'gross_amount' => 'Gross',
                'platform_commission_amount' => 'Commission',
                'status' => 'Status',
                $callDateColumn => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('calls.csv', $query, $cols);
        }

        $sessions = $query->paginate(20)->withQueryString();
        return view('admin.reports.calls', compact('sessions', 'range'));
    }

    public function chats(Request $request)
    {
        $range = $this->getRange($request);
        $query = ChatSession::with(['user', 'astrologerProfile.user'])
            ->whereBetween('updated_at', [$range['start_utc'], $range['end_utc']])
            ->latest('updated_at');

        if ($request->has('export')) {
            $cols = [
                'id' => 'Session ID',
                'user.name' => 'User',
                'astrologerProfile.user.name' => 'Astrologer',
                'total_charged' => 'Gross',
                'commission_amount_total' => 'Commission',
                'status' => 'Status',
                'updated_at' => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('chats.csv', $query, $cols);
        }

        $sessions = $query->paginate(20)->withQueryString();
        return view('admin.reports.chats', compact('sessions', 'range'));
    }

    public function aiChats(Request $request)
    {
        $range = $this->getRange($request);
        $query = AiChatSession::with('user')
            ->whereBetween('updated_at', [$range['start_utc'], $range['end_utc']])
            ->latest('updated_at');

        if ($request->has('export')) {
            $cols = [
                'id' => 'Session ID',
                'user.name' => 'User',
                'total_charged' => 'Gross',
                'commission_amount_total' => 'Commission',
                'status' => 'Status',
                'updated_at' => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('ai_chats.csv', $query, $cols);
        }

        $sessions = $query->paginate(20)->withQueryString();
        return view('admin.reports.ai_chats', compact('sessions', 'range'));
    }

    public function astrologers(Request $request)
    {
        $sort = $request->get('sort', 'calls_count');
        $direction = $request->get('direction', 'desc');

        $profiles = \App\Models\AstrologerProfile::with('user')
            ->withCount(['callSessions as calls_count', 'chatSessions as chats_count'])
            ->withSum('callSessions as calls_revenue', 'gross_amount')
            ->withSum('chatSessions as chats_revenue', 'total_charged')
            ->orderBy($sort, $direction)
            ->paginate(20);

        return view('admin.reports.astrologers', compact('profiles'));
    }

    protected function getRange(Request $request): array
    {
        $tz = 'Asia/Kolkata';
        $preset = $request->get('preset', 'last_7_days');
        $end = Carbon::now($tz);

        switch ($preset) {
            case 'today':
                $start = $end->copy()->startOfDay();
                break;
            case 'yesterday':
                $start = $end->copy()->subDay()->startOfDay();
                $end = $end->copy()->subDay()->endOfDay();
                break;
            case 'last_30_days':
                $start = $end->copy()->subDays(30)->startOfDay();
                break;
            case 'this_month':
                $start = $end->copy()->startOfMonth();
                break;
            case 'last_7_days':
            default:
                $start = $end->copy()->subDays(7)->startOfDay();
                break;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date, $tz)->startOfDay();
            $end = Carbon::parse($request->end_date, $tz)->endOfDay();
            $preset = 'custom';
        }

        return [
            'start_ist' => $start,
            'end_ist' => $end,
            'start_utc' => $start->copy()->setTimezone('UTC'),
            'end_utc' => $end->copy()->setTimezone('UTC'),
            'preset' => $preset,
            'timezone' => $tz,
        ];
    }

    protected function exportRevenueSummary(array $range)
    {
        $query = DailyMetric::whereBetween('date_ist', [$range['start_ist']->toDateString(), $range['end_ist']->toDateString()])
            ->orderBy('date_ist');

        $cols = [
            'date_ist' => 'Date (IST)',
            'call_gross' => 'Call Gross',
            'chat_gross' => 'Chat Gross',
            'ai_gross' => 'AI Gross',
            'call_commission' => 'Call Commission',
            'chat_commission' => 'Chat Commission',
            'ai_commission' => 'AI Commission',
            'call_earnings' => 'Call Earnings',
            'chat_earnings' => 'Chat Earnings',
            'ai_earnings' => 'AI Earnings',
        ];

        return app(\App\Services\CsvExportService::class)->streamExport('revenue_summary.csv', $query, $cols);
    }

    protected function exportRevenueItems(array $range, string $type)
    {
        $type = in_array($type, ['call', 'chat', 'ai'], true) ? $type : 'call';
        $query = $this->buildRevenueItemsQuery($type, $range);
        $query->orderBy('occurred_at');

        $cols = [
            'occurred_at' => 'Date (IST)',
            'type' => 'Type',
            'user_name' => 'User',
            'astrologer_name' => 'Astrologer',
            'gross' => 'Gross',
            'commission' => 'Commission',
            'earnings' => 'Earnings',
            'reference_id' => 'Reference ID',
        ];

        return app(\App\Services\CsvExportService::class)->streamExport("revenue_items_{$type}.csv", $query, $cols);
    }

    protected function buildRevenueItemsQuery(string $type, array $range)
    {
        if ($type === 'chat') {
            $commissionExpr = Schema::hasColumn('chat_sessions', 'commission_percent_snapshot')
                ? 'chat_message_charges.amount * (COALESCE(chat_sessions.commission_percent_snapshot, 0) / 100)'
                : '0';
            $earningsExpr = "chat_message_charges.amount - ({$commissionExpr})";

            $query = DB::table('chat_message_charges')
                ->join('chat_sessions', 'chat_sessions.id', '=', 'chat_message_charges.chat_session_id')
                ->leftJoin('users as users', 'users.id', '=', 'chat_sessions.user_id')
                ->whereBetween('chat_message_charges.created_at', [$range['start_utc'], $range['end_utc']]);

            $astrologerSelect = 'NULL as astrologer_name';
            if (Schema::hasColumn('chat_sessions', 'astrologer_profile_id')) {
                $query->leftJoin('astrologer_profiles', 'astrologer_profiles.id', '=', 'chat_sessions.astrologer_profile_id')
                    ->leftJoin('users as astrologers', 'astrologers.id', '=', 'astrologer_profiles.user_id');
                $astrologerSelect = 'astrologers.name as astrologer_name';
            } elseif (Schema::hasColumn('chat_sessions', 'astrologer_user_id')) {
                $query->leftJoin('users as astrologers', 'astrologers.id', '=', 'chat_sessions.astrologer_user_id');
                $astrologerSelect = 'astrologers.name as astrologer_name';
            }

            return $query->selectRaw("chat_message_charges.created_at as occurred_at, 'chat' as type, users.name as user_name, {$astrologerSelect}, chat_message_charges.amount as gross, {$commissionExpr} as commission, {$earningsExpr} as earnings, chat_message_charges.firestore_message_id as reference_id");
        }

        if ($type === 'ai') {
            $commissionExpr = Schema::hasColumn('ai_chat_sessions', 'commission_percent_snapshot')
                ? 'ai_message_charges.amount * (COALESCE(ai_chat_sessions.commission_percent_snapshot, 0) / 100)'
                : '0';
            $earningsExpr = "ai_message_charges.amount - ({$commissionExpr})";

            return DB::table('ai_message_charges')
                ->join('ai_chat_sessions', 'ai_chat_sessions.id', '=', 'ai_message_charges.ai_chat_session_id')
                ->leftJoin('users as users', 'users.id', '=', 'ai_chat_sessions.user_id')
                ->whereBetween('ai_message_charges.created_at', [$range['start_utc'], $range['end_utc']])
                ->selectRaw("ai_message_charges.created_at as occurred_at, 'ai' as type, users.name as user_name, NULL as astrologer_name, ai_message_charges.amount as gross, {$commissionExpr} as commission, {$earningsExpr} as earnings, ai_message_charges.client_message_id as reference_id");
        }

        $callDateColumn = $this->callDateColumn();
        $grossColumn = $this->callGrossColumn();
        $commissionColumn = $this->callCommissionColumn();
        $earningsColumn = $this->callEarningsColumn();

        $grossExpr = "call_sessions.{$grossColumn}";
        if ($commissionColumn) {
            $commissionExpr = "call_sessions.{$commissionColumn}";
        } elseif ($earningsColumn) {
            $commissionExpr = "({$grossExpr} - call_sessions.{$earningsColumn})";
        } else {
            $commissionExpr = '0';
        }

        if ($earningsColumn) {
            $earningsExpr = "call_sessions.{$earningsColumn}";
        } else {
            $earningsExpr = "({$grossExpr} - ({$commissionExpr}))";
        }

        $query = DB::table('call_sessions')
            ->leftJoin('users as users', 'users.id', '=', 'call_sessions.user_id')
            ->when(Schema::hasColumn('call_sessions', 'status'), function ($query) {
                $query->where('status', 'completed');
            })
            ->when(Schema::hasColumn('call_sessions', 'settled_at'), function ($query) {
                $query->whereNotNull('settled_at');
            })
            ->whereBetween("call_sessions.{$callDateColumn}", [$range['start_utc'], $range['end_utc']]);

        $astrologerSelect = 'NULL as astrologer_name';
        if (Schema::hasColumn('call_sessions', 'astrologer_profile_id')) {
            $query->leftJoin('astrologer_profiles', 'astrologer_profiles.id', '=', 'call_sessions.astrologer_profile_id')
                ->leftJoin('users as astrologers', 'astrologers.id', '=', 'astrologer_profiles.user_id');
            $astrologerSelect = 'astrologers.name as astrologer_name';
        } elseif (Schema::hasColumn('call_sessions', 'astrologer_user_id')) {
            $query->leftJoin('users as astrologers', 'astrologers.id', '=', 'call_sessions.astrologer_user_id');
            $astrologerSelect = 'astrologers.name as astrologer_name';
        }

        return $query->selectRaw("call_sessions.{$callDateColumn} as occurred_at, 'call' as type, users.name as user_name, {$astrologerSelect}, {$grossExpr} as gross, {$commissionExpr} as commission, {$earningsExpr} as earnings, call_sessions.id as reference_id");
    }

    protected function callDateColumn(): string
    {
        if (Schema::hasColumn('call_sessions', 'settled_at')) {
            return 'settled_at';
        }
        if (Schema::hasColumn('call_sessions', 'ended_at_utc')) {
            return 'ended_at_utc';
        }
        if (Schema::hasColumn('call_sessions', 'ended_at')) {
            return 'ended_at';
        }
        return 'updated_at';
    }

    protected function callGrossColumn(): string
    {
        return Schema::hasColumn('call_sessions', 'gross_amount') ? 'gross_amount' : 'cost';
    }

    protected function callCommissionColumn(): ?string
    {
        return Schema::hasColumn('call_sessions', 'platform_commission_amount') ? 'platform_commission_amount' : null;
    }

    protected function callEarningsColumn(): ?string
    {
        return Schema::hasColumn('call_sessions', 'astrologer_earnings_amount') ? 'astrologer_earnings_amount' : null;
    }

    protected function countActiveUsers(Carbon $startUtc, Carbon $endUtc): int
    {
        $callDateColumn = $this->callDateColumn();

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

        return DB::query()
            ->fromSub(
                $walletActivity->union($callActivity)->union($chatActivity)->union($aiActivity),
                'activity'
            )
            ->distinct()
            ->count('user_id');
    }
}

