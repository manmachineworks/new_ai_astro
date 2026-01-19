<?php

namespace App\Http\Controllers\Astrologer;

use App\Models\CallLog;
use App\Models\ChatSession;
use App\Models\Earning;
use Inertia\Inertia;

class OverviewController extends AstrologerBaseController
{
    public function index()
    {
        $astrologer = $this->resolveAstrologer();

        $callCount = CallLog::where('astrologer_id', $astrologer->id)->count();
        $chatCount = ChatSession::where('astrologer_id', $astrologer->id)->count();
        $totalEarned = Earning::where('astrologer_id', $astrologer->id)->sum('net_amount');

        $recentCalls = CallLog::where('astrologer_id', $astrologer->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentChats = ChatSession::where('astrologer_id', $astrologer->id)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Astrologer/Overview', [
            'stats' => [
                'calls' => $callCount,
                'chats' => $chatCount,
                'earnings' => $totalEarned,
            ],
            'recentCalls' => $recentCalls,
            'recentChats' => $recentChats,
        ]);
    }
}
