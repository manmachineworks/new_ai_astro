<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totals = [
            'users' => User::count(),
            'astrologers' => User::role('Astrologer')->count(),
            'revenue' => Schema::hasTable('transactions')
                ? (float) DB::table('transactions')->sum('amount')
                : 0.0,
            'today_calls' => Schema::hasTable('call_logs')
                ? (int) DB::table('call_logs')->whereDate('created_at', now()->toDateString())->count()
                : 0,
            'today_chats' => Schema::hasTable('chat_logs')
                ? (int) DB::table('chat_logs')->whereDate('created_at', now()->toDateString())->count()
                : 0,
        ];

        $chart = [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'daily' => [12, 19, 7, 14, 9, 11, 5],
            'weekly' => [120, 140, 110, 180],
            'monthly' => [820, 910, 760, 1020, 880, 940],
        ];

        return view('admin.dashboard', compact('totals', 'chart'));
    }
}
