<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * List all support tickets with filters
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'messages']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(50);

        return view('admin.support.index', compact('tickets'));
    }

    /**
     * View ticket detail
     */
    public function show(string $id)
    {
        $ticket = SupportTicket::with([
            'user',
            'messages' => function ($query) {
                $query->orderBy('created_at');
            }
        ])->findOrFail($id);

        return view('admin.support.show', compact('ticket'));
    }

    /**
     * Reply to ticket
     */
    public function reply(Request $request, string $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::findOrFail($id);

        $ticket->addMessage('admin', $request->user()->id, $validated['message']);

        // Update status to pending if was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'pending']);
        }

        return back()->with('success', 'Reply sent successfully');
    }

    /**
     * Close ticket
     */
    public function close(Request $request, string $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->close();

        return back()->with('success', 'Ticket closed');
    }

    /**
     * Resolve ticket
     */
    public function resolve(Request $request, string $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->markResolved();

        return back()->with('success', 'Ticket marked as resolved');
    }
}
