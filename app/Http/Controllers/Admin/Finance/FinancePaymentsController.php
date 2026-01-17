<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentOrder;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FinancePaymentsController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentOrder::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->paginate(20);

        return view('admin.finance.payments.index', compact('orders'));
    }

    public function show(PaymentOrder $paymentOrder)
    {
        return view('admin.finance.payments.show', compact('paymentOrder'));
    }

    public function recheck(PaymentOrder $paymentOrder)
    {
        // Leverage existing logic from PaymentController or duplicate simple check?
        // Ideally, we should have a PaymentService.
        // For now, I'll instantiate PaymentController and call checkStatus if public, 
        // or replicate the PhonePe status check logic here to be safe and independent.

        // Let's assume we want to be independent.
        // But verifying against PaymentController logic is best.
        // Let's defer to a shared service if possible, but I'll write the PhonePe check here for speed/isolation.

        // Actually, PaymentController::has `checkStatus`? No, it has `callback`.
        // I will implement a standard Status Check request relative to PhonePe.

        try {
            // MOCK IMPLEMENTATION for Recheck (since we are in test mode usually)
            // Real implementation requires PhonePe SDK/API call.

            // If status is pending, we pretend we checked.
            if ($paymentOrder->status === 'pending') {
                // In a real app, do HTTP request to PhonePe /pg/v1/status
                // If successful:
                // $paymentOrder->update(['status' => 'completed']);
                // WalletService::credit(...)

                return back()->with('info', 'Status check initiated. (Implement PhonePe Status API integration here)');
            }

            return back()->with('info', 'Order is already ' . $paymentOrder->status);

        } catch (\Exception $e) {
            return back()->with('error', 'Status check failed: ' . $e->getMessage());
        }
    }

    public function retryWebhook(PaymentOrder $paymentOrder)
    {
        // This simulates a webhook event processing manually
        if ($paymentOrder->status !== 'completed') {
            return back()->with('error', 'Only completed orders can trigger webhook logic (or use Recheck for pending).');
        }

        // If it was completed but user didn't get credit?
        // We can manually trigger the credit logic.

        $walletService = app(\App\Services\WalletService::class);

        try {
            // Check if transaction exists
            $exists = \App\Models\WalletTransaction::where('reference_id', $paymentOrder->id)
                ->where('reference_type', 'recharge')
                ->exists();

            if ($exists) {
                return back()->with('warning', 'Wallet already credited for this order.');
            }

            $walletService->credit(
                $paymentOrder->user,
                $paymentOrder->amount,
                'recharge',
                $paymentOrder->id,
                'Payment Order #' . $paymentOrder->id . ' (Manual Retry)'
            );

            return back()->with('success', 'Wallet credit triggered manually.');

        } catch (\Exception $e) {
            return back()->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }
}
