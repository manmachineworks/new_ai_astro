<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerProfile;
use App\Models\WalletTransaction;
use App\Models\CallSession;
use App\Models\AiChatSession;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $walletBalance = $user->wallet_balance ?? 0.00;
        $lowBalanceThreshold = 100.00;

        // Fetch Featured/Top Astrologers
        // Logic: Verified + Enabled + (Featured flag if exists, or High Rating + Online)
        // Adjust query based on your DB schema reality. assuming 'is_verified' and 'is_enabled'
        $featuredAstrologers = AstrologerProfile::with('user')
            ->where('verification_status', 'verified')
            ->where('is_enabled', true)
            ->where('visibility', true)
            ->orderByDesc('rating_avg')
            ->limit(10)
            ->get()
            ->map(function ($astro) {
                return [
                    'id' => $astro->id,
                    'name' => $astro->display_name ?? $astro->user->name,
                    'specialties' => is_array($astro->specialties) ? implode(', ', array_slice($astro->specialties, 0, 2)) : 'Vedic',
                    'rating' => number_format($astro->rating_avg ?? 0, 1),
                    'price_per_min' => (int) ($astro->call_per_minute ?? 0),
                    'profile_image' => $astro->profile_photo_path,
                    'online' => (bool) cache("astro_online_{$astro->id}", false), // Example cache usage, or assume offline
                ];
            });

        // Recent Activity (Calls & Chats)
        // Merge CallSession and AiChatSession or just show calls for now?
        // Let's pull Calls logs
        $recentCalls = CallSession::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->with('astrologerProfile')
            ->get()
            ->map(function ($session) {
                return [
                    'description' => 'Call with ' . ($session->astrologerProfile->display_name ?? 'Astrologer'),
                    'date' => $session->created_at->diffForHumans(),
                    'amount' => $session->gross_amount,
                    'type' => 'call'
                ];
            });

        // Wallet Transactions (Recharges/Spends)
        $recentTransactions = WalletTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($txn) {
                return [
                    'description' => $txn->description ?? ucfirst($txn->type),
                    'date' => $txn->created_at->diffForHumans(),
                    'amount' => $txn->amount,
                    'type' => $txn->type // credit/debit
                ];
            });

        // Merge and sort purely by date logic is clearer if needed, but separate lists in View might be easier?
        // The view 'overview.blade.php' iterates $recentActivity. Let's map transactions to that format.
        // Actually, let's use Transactions as the source of truth for "Activity" (spends/adds).
        $recentActivity = $recentTransactions;

        $upcomingAppointments = $user->appointments()
            ->upcoming()
            ->orderBy('start_at_utc')
            ->limit(3)
            ->with('astrologerProfile')
            ->get();

        $aiChatStats = [
            'sessions' => AiChatSession::where('user_id', $user->id)->count(),
            // 'spend' => AiChatSession::where('user_id', $user->id)->sum('cost') // if cost column exists
            'spend' => 0 // Placeholder
        ];

        return view('user.dashboard.overview', compact(
            'walletBalance',
            'lowBalanceThreshold',
            'featuredAstrologers',
            'recentActivity',
            'upcomingAppointments',
            'aiChatStats'
        ));
    }
}
