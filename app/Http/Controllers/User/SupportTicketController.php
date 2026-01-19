<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('user.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('user.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            // 'attachments.*' => 'file|mimes:jpg,png,pdf|max:2048'
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'status' => 'open',
            'priority' => 'medium', // Default
        ]);

        // Add initial message as description
        $ticket->addMessage('user', auth()->id(), $request->description);

        return redirect()->route('user.support.index')
            ->with('success', 'Ticket created successfully! Ticket ID: #' . $ticket->id);
    }

    public function show($ticketId)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('id', $ticketId)
            ->with('messages')
            ->firstOrFail();

        // Transform messages for view if needed, or update view to use model attributes
        // Current view expects 'thread' array with keys: is_user, sender, message, time
        $thread = $ticket->messages->map(function ($msg) {
            return [
                'is_user' => $msg->sender_type === 'user',
                'sender' => $msg->sender_type === 'user' ? 'You' : 'Support Agent',
                'message' => $msg->message,
                'time' => $msg->created_at->format('h:i A, M d')
            ];
        });

        return view('user.support.show', compact('ticket', 'thread'));
    }

    public function reply(Request $request, $ticketId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $ticket = SupportTicket::where('user_id', auth()->id())
            ->where('id', $ticketId)
            ->firstOrFail();

        if ($ticket->status === 'closed') {
            return back()->with('error', 'This ticket is closed.');
        }

        $ticket->addMessage('user', auth()->id(), $request->message);

        // Re-open if it was resolved but user replied?
        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent.');
    }
}
