<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;

class AdminWebhookController extends Controller
{
    public function index(Request $request)
    {
        $query = WebhookEvent::latest();

        // Search by External ID or Event Type
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('external_id', 'like', "%{$search}%")
                    ->orWhere('event_type', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }
        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->where('processing_status', 'completed');
            } elseif ($request->status === 'failed') {
                $query->where('processing_status', 'failed');
            } elseif ($request->status === 'pending') {
                $query->where('processing_status', 'pending');
            }
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        // Dead-letter filter
        if ($request->input('filter') === 'dead_letter') {
            $query->where('processing_status', 'failed')
                ->where('attempts', '>=', 3);
        }

        $events = $query->paginate(20)->withQueryString();

        return view('admin.webhooks.index', compact('events'));
    }

    public function show($id)
    {
        $event = WebhookEvent::findOrFail($id);
        return view('admin.webhooks.show', compact('event'));
    }

    public function retry($id)
    {
        $event = WebhookEvent::findOrFail($id);

        $event->update([
            'processing_status' => 'pending',
            'error_message' => null,
            'attempts' => ($event->attempts ?? 0) + 1,
            'processed_at' => null
        ]);

        \App\Services\AdminActivityLogger::log('webhook.retry', null, ['webhook_id' => $event->id]);

        return back()->with('success', 'Webhook queued for retry.');
    }
}
