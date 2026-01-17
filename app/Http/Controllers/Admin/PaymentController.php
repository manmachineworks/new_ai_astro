<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentOrder::with('user');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('merchant_transaction_id', 'like', "%{$search}%")
                    ->orWhere('provider_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Stats Aggregation
        $statsQuery = clone $query;
        $aggregates = [
            'total_transactions' => $statsQuery->count(),
            'total_amount' => $statsQuery->sum('amount'), // Assuming amount is always positive for revenue
            'successful_amount' => (clone $statsQuery)->where('status', 'completed')->sum('amount'),
            'failed_count' => (clone $statsQuery)->where('status', 'failed')->count(),
        ];

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('orders', 'aggregates'));
    }

    public function show($id)
    {
        $order = PaymentOrder::with(['user'])->findOrFail($id);

        // Fetch related webhooks manually or via relationship if added
        // For audit, finding by external_id (merchant_transaction_id)
        $webhooks = \App\Models\WebhookEvent::where('external_id', $order->merchant_transaction_id)
            ->latest()
            ->get();

        return view('admin.payments.show', compact('order', 'webhooks'));
    }
}
