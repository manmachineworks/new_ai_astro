<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * List user's support tickets
     */
    public function index(Request $request)
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->with('messages')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $tickets->through(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'messages_count' => $ticket->messages->count(),
                    'created_at' => $ticket->created_at->toIso8601String(),
                    'updated_at' => $ticket->updated_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Create new support ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:payment,call,chat,ai_chat,appointment,account,other',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'status' => 'open',
        ]);

        // Add first message
        $ticket->addMessage('user', $request->user()->id, $validated['message']);

        return response()->json([
            'ticket_id' => $ticket->id,
            'status' => $ticket->status,
        ], 201);
    }

    /**
     * View ticket detail with message thread
     */
    public function show(Request $request, string $id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at');
                }
            ])
            ->firstOrFail();

        return response()->json([
            'id' => $ticket->id,
            'subject' => $ticket->subject,
            'category' => $ticket->category,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'created_at' => $ticket->created_at->toIso8601String(),
            'messages' => $ticket->messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Add message to ticket
     */
    public function addMessage(Request $request, string $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $message = $ticket->addMessage('user', $request->user()->id, $validated['message']);

        // Reopen if closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return response()->json([
            'message_id' => $message->id,
            'created_at' => $message->created_at->toIso8601String(),
        ], 201);
    }
}
