<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $sessions = ChatSession::where('user_id', auth()->id())
            ->with(['astrologerProfile', 'astrologer'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($session) {
                $astro = $session->astrologerProfile;
                return [
                    'id' => $session->id,
                    'astrologer_name' => $astro->display_name ?? 'Astrologer',
                    'astrologer_image' => $astro->profile_photo_path ?? null,
                    'last_message' => 'View conversation', // Actual msg might be in Firebase or separate table?
                    'last_message_time' => $session->updated_at->diffForHumans(), // or last_message_at
                    'unread' => 0, // Need read receipt logic
                    'online' => (bool) cache("astro_online_{$astro->id}", false)
                ];
            });

        return view('user.chat.index', compact('sessions'));
    }

    public function show($sessionId)
    {
        $activeSession = ChatSession::where('user_id', auth()->id())
            ->where('id', $sessionId) // Assuming threadId matches sessionId in this context
            ->with('astrologerProfile')
            ->firstOrFail();

        // Check if session is active or if user can afford to continue?
        // If session is 'completed', maybe read-only mode?
        // For now, let's just use checkBalance as a general gate for *new* interactions
        if ($activeSession->status === 'active' && !$this->checkBalance(50)) {
            return redirect()->route('user.wallet.recharge')
                ->with('error', 'Insufficient balance to continue chat. Please recharge.');
        }

        // Re-fetch list for sidebar
        $sessions = ChatSession::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->with('astrologerProfile')
            ->get()
            ->map(function ($s) {
                $astro = $s->astrologerProfile;
                return [
                    'id' => $s->id,
                    'astrologer_name' => $astro->display_name ?? 'Astrologer',
                    'astrologer_image' => $astro->profile_photo_path ?? null,
                    'last_message' => 'View conversation',
                    'last_message_time' => $s->updated_at->diffForHumans(),
                    'unread' => 0,
                    'online' => false
                ];
            });

        // Current UI expects 'messages' array. In real app, this might be fetched via AJAX from Firebase 
        // or a separate Message model.
        // We'll pass an empty array or fetch from a local Message table if it existed.
        // Since we are likely using Firebase/MySQL hybrid, let's pass a placeholder instructions array for now.
        $messages = [];

        return view('user.chat.show', compact('sessions', 'activeSession', 'messages'));
    }

    private function checkBalance($requiredAmount)
    {
        $user = auth()->user();
        $balance = $user->wallet_balance ?? 0.00;
        return $balance >= $requiredAmount;
    }
}
