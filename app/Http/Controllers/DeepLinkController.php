<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeepLinkController extends Controller
{
    public function handle(Request $request, $type, $id)
    {
        // 1. Auth Check
        if (!Auth::check()) {
            return redirect()->route('login')->with('url.intended', url()->current());
        }

        $user = Auth::user();

        // 2. Route Dispatcher based on Type
        switch ($type) {
            case 'chat':
                return $this->handleChat($user, $id);
            case 'call':
                return $this->handleCall($user, $id);
            case 'appointment':
                return $this->handleAppointment($user, $id);
            case 'ai-chat':
                return $this->handleAiChat($user, $id);
            default:
                return redirect()->route('home')->with('error', 'Invalid link type.');
        }
    }

    protected function handleChat($user, $conversationId)
    {
        // Check if session exists/user belongs to it
        $session = \App\Models\ChatSession::where('conversation_id', $conversationId)->first();

        if (!$session) {
            return redirect()->route('user.dashboard')->with('error', 'Chat session not found.');
        }

        // Authorization: User OR Astrologer linked to session
        if ($session->user_id !== $user->id && $session->astrologer_profile_id !== $user->astrologerProfile?->id) {
            return abort(403, 'Unauthorized access to this chat.');
        }

        // Astrologer logic (if astrologer is logged in) vs User logic
        if ($user->hasRole('Astrologer') && $session->astrologer_profile_id === $user->astrologerProfile?->id) {
            return redirect()->route('astrologer.chats', ['conversation_id' => $conversationId]);
        }

        return redirect()->route('user.chats.show', $conversationId);
    }

    protected function handleCall($user, $callSessionId)
    {
        // Logic similar to chat, check participation
        // For now redirect to generic calls page if specific ID implementation varies
        return redirect()->route('user.calls');
    }

    protected function handleAppointment($user, $appointmentId)
    {
        $appt = \App\Models\Appointment::find($appointmentId);
        if (!$appt) {
            return redirect()->route('appointments.index')->with('error', 'Appointment not found.');
        }

        if ($appt->user_id !== $user->id && $appt->astrologer_profile_id !== $user->astrologerProfile?->id) {
            return abort(403, 'Unauthorized');
        }

        return redirect()->route('appointments.show', $appointmentId);
    }

    protected function handleAiChat($user, $sessionId)
    {
        return redirect()->route('user.ai_chat.show', $sessionId);
    }
}
