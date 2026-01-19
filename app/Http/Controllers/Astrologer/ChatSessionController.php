<?php

namespace App\Http\Controllers\Astrologer;

use App\Models\ChatSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatSessionController extends AstrologerBaseController
{
    public function index(Request $request)
    {
        $astrologer = $this->resolveAstrologer();
        $query = ChatSession::where('astrologer_id', $astrologer->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Astrologer/Chats/Index', [
            'sessions' => $sessions,
        ]);
    }

    public function show(ChatSession $session)
    {
        $this->assertOwnership($session);
        return Inertia::render('Astrologer/Chats/Show', [
            'session' => $session,
        ]);
    }

    public function close(ChatSession $session)
    {
        $this->assertOwnership($session);
        $session->update([
            'status' => 'closed',
            'ended_at' => now(),
        ]);

        return back()->with('success', 'Chat closed.');
    }

    public function block(ChatSession $session)
    {
        $this->assertOwnership($session);
        $session->update(['status' => 'blocked']);

        return back()->with('success', 'Chat blocked.');
    }

    protected function assertOwnership(ChatSession $session): void
    {
        $astrologer = $this->resolveAstrologer();
        abort_unless($session->astrologer_id === $astrologer->id, 403);
    }
}
