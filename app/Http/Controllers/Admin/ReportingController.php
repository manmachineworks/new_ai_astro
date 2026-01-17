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

class ReportingController extends Controller
{
    public function dashboard(Request $request)
    {
        $range = $this->getRange($request);
        $start = $range['start'];
        $end = $range['end'];

        $cacheKey = "admin_report_summary_{$start->toDateString()}_{$end->toDateString()}";

        $data = Cache::remember($cacheKey, 300, function () use ($start, $end) {
            // Aggregates from daily_metrics for efficiency
            $metrics = DailyMetric::whereBetween('date_ist', [$start->toDateString(), $end->toDateString()])
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
                    MAX(active_users) as peak_active_users,
                    SUM(new_users) as total_new_users
                ')->first();

            $totalGross = ($metrics->call_gross ?? 0) + ($metrics->chat_gross ?? 0) + ($metrics->ai_gross ?? 0);
            $totalComm = ($metrics->call_commission ?? 0) + ($metrics->chat_commission ?? 0) + ($metrics->ai_commission ?? 0);
            $totalEarn = ($metrics->call_earnings ?? 0) + ($metrics->chat_earnings ?? 0) + ($metrics->ai_earnings ?? 0);

            $rechargeTotal = $metrics->recharge_total ?? 0;
            $rechargeOk = $metrics->recharge_count_ok ?? 0;
            $rechargeFail = $metrics->recharge_count_fail ?? 0;
            $rechargeRate = ($rechargeOk + $rechargeFail) > 0 ? ($rechargeOk / ($rechargeOk + $rechargeFail)) * 100 : 0;

            // Daily chart data
            $chartData = DailyMetric::whereBetween('date_ist', [$start->toDateString(), $end->toDateString()])
                ->orderBy('date_ist')
                ->get(['date_ist', 'call_gross', 'chat_gross', 'ai_gross']);

            return compact('totalGross', 'totalComm', 'totalEarn', 'rechargeTotal', 'rechargeRate', 'metrics', 'chartData');
        });

        return view('admin.reports.dashboard', array_merge($data, ['filters' => $range]));
    }

    public function revenue(Request $request)
    {
        $range = $this->getRange($request);
        if ($request->has('export')) {
            $query = DailyMetric::whereBetween('date_ist', [$range['start']->toDateString(), $range['end']->toDateString()])
                ->orderBy('date_ist');

            $cols = [
                'date_ist' => 'Date',
                'call_gross' => 'Call Gross',
                'chat_gross' => 'Chat Gross',
                'ai_gross' => 'AI Gross',
                'call_commission' => 'Call Commission',
                'chat_commission' => 'Chat Commission',
                'ai_commission' => 'AI Commission',
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('revenue_summary.csv', $query, $cols);
        }
        return view('admin.reports.revenue', compact('range'));
    }

    public function recharges(Request $request)
    {
        $range = $this->getRange($request);
        $query = PaymentOrder::with('user')
            ->whereBetween('updated_at', [$range['start']->setTimezone('UTC'), $range['end']->setTimezone('UTC')])
            ->latest();

        if ($request->has('export')) {
            $cols = [
                'merchant_transaction_id' => 'Transaction ID',
                'user.name' => 'User',
                'amount' => 'Amount',
                'status' => 'Status',
                'updated_at' => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('recharges.csv', $query, $cols);
        }

        $orders = $query->paginate(20);
        return view('admin.reports.recharges', compact('orders', 'range'));
    }

    public function calls(Request $request)
    {
        $range = $this->getRange($request);
        $query = CallSession::with(['user', 'astrologerProfile.user'])
            ->whereBetween('updated_at', [$range['start']->setTimezone('UTC'), $range['end']->setTimezone('UTC')])
            ->latest();

        if ($request->has('export')) {
            $cols = [
                'id' => 'Session ID',
                'user.name' => 'User',
                'astrologerProfile.user.name' => 'Astrologer',
                'gross_amount' => 'Gross',
                'platform_commission_amount' => 'Commission',
                'status' => 'Status',
                'updated_at' => 'Date'
            ];
            return app(\App\Services\CsvExportService::class)->streamExport('calls.csv', $query, $cols);
        }

        $sessions = $query->paginate(20);
        return view('admin.reports.calls', compact('sessions', 'range'));
    }

    public function chats(Request $request)
    {
        $range = $this->getRange($request);
        $query = ChatSession::with(['user', 'astrologerProfile.user'])
            ->whereBetween('updated_at', [$range['start']->setTimezone('UTC'), $range['end']->setTimezone('UTC')])
            ->latest();

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

        $sessions = $query->paginate(20);
        return view('admin.reports.chats', compact('sessions', 'range'));
    }

    public function aiChats(Request $request)
    {
        $range = $this->getRange($request);
        $query = AiChatSession::with('user')
            ->whereBetween('updated_at', [$range['start']->setTimezone('UTC'), $range['end']->setTimezone('UTC')])
            ->latest();

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

        $sessions = $query->paginate(20);
        return view('admin.reports.ai_chats', compact('sessions', 'range'));
    }

    public function astrologers(Request $request)
    {
        $profiles = \App\Models\AstrologerProfile::with('user')
            ->withCount(['callSessions as calls_count', 'chatSessions as chats_count'])
            ->get();
        return view('admin.reports.astrologers', compact('profiles'));
    }

    protected function getRange(Request $request)
    {
        $preset = $request->get('preset', 'last_7_days');
        $end = Carbon::now('Asia/Kolkata');

        switch ($preset) {
            case 'today':
                $start = $end->copy()->startOfDay();
                break;
            case 'yesterday':
                $start = $end->copy()->subDay()->startOfDay();
                $end = $end->copy()->subDay()->endOfDay();
                break;
            case 'last_30_days':
                $start = $end->copy()->subDays(30);
                break;
            case 'this_month':
                $start = $end->copy()->startOfMonth();
                break;
            case 'last_7_days':
            default:
                $start = $end->copy()->subDays(7);
                break;
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $start = Carbon::parse($request->start_date, 'Asia/Kolkata')->startOfDay();
            $end = Carbon::parse($request->end_date, 'Asia/Kolkata')->endOfDay();
        }

        return [
            'start' => $start,
            'end' => $end,
            'preset' => $preset
        ];
    }
}
