<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = PaymentOrder::with('user')->latest()->paginate(20);
        return view('admin.payments.index', compact('orders'));
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
