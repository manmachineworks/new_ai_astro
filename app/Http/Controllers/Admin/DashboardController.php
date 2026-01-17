<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', 'today'); // today, yesterday, 7days, 30days, this_month, all
        $dates = $this->parseDateRange($range);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        $rechargesSuccess = $this->applyRechargeTypeFilter(
            DB::table('payment_orders')
                ->where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->count();
        $rechargesFailed = $this->applyRechargeTypeFilter(
            DB::table('payment_orders')
                ->where('status', 'failed')
                ->whereBetween('created_at', [$startDate, $endDate])
        )->count();
        $failedWebhooks = $this->applyFailedWebhookFilter(
            DB::table('webhook_events')->whereBetween('created_at', [$startDate, $endDate])
        )->count();

        // KPIs
        $totals = [
            'users' => User::where('created_at', '<=', $endDate)->count(),
            'astrologers' => User::role('Astrologer')->where('created_at', '<=', $endDate)->count(),
            'revenue' => DB::table('payment_orders')
                ->where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),

            // Activity counts for the selected range
            'calls_count' => DB::table('call_sessions')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'chats_count' => DB::table('chat_sessions')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'ai_chats_count' => DB::table('ai_chat_sessions')->whereBetween('created_at', [$startDate, $endDate])->count(),

            'recharges_success' => $rechargesSuccess,
            'recharges_failed' => $rechargesFailed,

            'pending_verifications' => DB::table('astrologer_profiles')->where('is_verified', 0)->count(), // Global count

            'commission' => $this->calculateCommission($startDate, $endDate),

            'failed_webhooks' => $failedWebhooks,
        ];

        // Chart Data (Activity over the selected range)
        $chart = $this->getActivityChartData($startDate, $endDate);

        // Widgets
        $widgets = [
            'pending_verifications' => DB::table('astrologer_profiles')
                ->join('users', 'astrologer_profiles.user_id', '=', 'users.id')
                ->where('astrologer_profiles.is_verified', 0)
                ->select('users.name', 'users.email', 'astrologer_profiles.created_at', 'users.id')
                ->limit(10)
                ->get(),
            'latest_payments' => DB::table('payment_orders')
                ->join('users', 'payment_orders.user_id', '=', 'users.id')
                ->where('status', 'success')
                ->select('payment_orders.id', 'users.name', 'payment_orders.amount', 'payment_orders.created_at', 'payment_orders.status')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'system_alerts' => $this->getSystemAlerts(),
        ];

        return view('admin.dashboard', compact('totals', 'chart', 'widgets', 'range', 'startDate', 'endDate'));
    }

    private function parseDateRange($range)
    {
        $end = now()->endOfDay();
        $start = now()->startOfDay();

        switch ($range) {
            case 'today':
                $start = now()->startOfDay();
                break;
            case 'yesterday':
                $start = now()->subDay()->startOfDay();
                $end = now()->subDay()->endOfDay();
                break;
            case '7days':
                $start = now()->subDays(6)->startOfDay();
                break;
            case '30days':
                $start = now()->subDays(29)->startOfDay();
                break;
            case 'this_month':
                $start = now()->startOfMonth();
                break;
            case 'custom':
                // Custom would usually come from startDate/endDate params, handled if needed
                // for now defaulting to 30 days if not flexible
                $start = now()->subDays(29)->startOfDay();
                break;
            case 'all':
                $start = now()->subYears(10);
                break;
        }

        // If Custom logic were fully implemented, we'd check $request->start_date
        if (request('start_date') && request('end_date')) {
            $start = \Carbon\Carbon::parse(request('start_date'))->startOfDay();
            $end = \Carbon\Carbon::parse(request('end_date'))->endOfDay();
        }

        return ['start' => $start, 'end' => $end];
    }

    private function calculateCommission($start, $end)
    {
        $callComm = 0;
        if (Schema::hasColumn('call_sessions', 'platform_commission_amount')) {
            $callComm = DB::table('call_sessions')->whereBetween('created_at', [$start, $end])->sum('platform_commission_amount') ?? 0;
        }

        $chatComm = 0;
        if (Schema::hasColumn('chat_sessions', 'commission_amount_total')) {
            $chatComm = DB::table('chat_sessions')->whereBetween('created_at', [$start, $end])->sum('commission_amount_total') ?? 0;
        }
        return $callComm + $chatComm;
    }

    private function getActivityChartData($start, $end)
    {
        $labels = [];
        $data = ['calls' => [], 'chats' => [], 'recharges' => []];

        $diff = $start->diffInDays($end);
        if ($diff > 31) {
            // If range is large, maybe group by week or month? 
            // keeping it daily for now but ideally should auto-scale
        }

        $period = \Carbon\CarbonPeriod::create($start, '1 day', $end);
        $dates = [];
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $dates[$d] = 0;
        }

        // Fetch Data
        $calls = DB::table('call_sessions')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();

        $chats = DB::table('chat_sessions')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();

        $recharges = $this->applyRechargeTypeFilter(
            DB::table('payment_orders')
                ->where('status', 'success')
        )
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('count', 'date')->toArray();

        foreach ($dates as $dateStr => $val) {
            $data['calls'][] = $calls[$dateStr] ?? 0;
            $data['chats'][] = $chats[$dateStr] ?? 0;
            $data['recharges'][] = $recharges[$dateStr] ?? 0;
        }

        return ['labels' => $labels, 'series' => $data];
    }

    private function getSystemAlerts()
    {
        $alerts = [];
        // Failed Webhooks
        $failedWebhooks = $this->applyFailedWebhookFilter(
            DB::table('webhook_events')
        )
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($failedWebhooks as $wh) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Webhook Failed: {$wh->provider}",
                'link' => route('admin.webhooks.index', ['status' => 'failed']),
                'time' => $wh->created_at
            ];
        }

        return $alerts;
    }

    private function applyRechargeTypeFilter($query)
    {
        if (Schema::hasColumn('payment_orders', 'type')) {
            $query->where('type', 'wallet_recharge');
        }

        return $query;
    }

    private function applyFailedWebhookFilter($query)
    {
        return $query->where('processing_status', 'failed');
    }
}
