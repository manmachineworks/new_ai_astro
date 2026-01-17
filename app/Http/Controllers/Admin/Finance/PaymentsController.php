<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Jobs\CheckPhonePePaymentStatus;
use App\Jobs\ProcessPhonePeWebhook;
use App\Models\PaymentOrder;
use App\Models\WalletTransaction;
use App\Models\WebhookEvent;
use App\Services\AdminActivityLogger;
use App\Services\CsvExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildPaymentsQuery($request);

        $orders = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('admin.finance.payments.index', [
            'orders' => $orders,
            'filters' => $this->filters($request),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function export(Request $request, CsvExportService $csv)
    {
        $query = $this->buildPaymentsQuery($request)->orderBy('created_at');

        $cols = [
            'merchant_transaction_id' => 'Merchant Txn ID',
            'provider_transaction_id' => 'Provider Txn ID',
            'user.name' => 'User',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At (IST)',
            'wallet_credit_count' => 'Wallet Credited',
        ];

        return $csv->streamExport('finance_payments.csv', $query, $cols);
    }

    public function show(PaymentOrder $paymentOrder)
    {
        $walletCredit = $this->findWalletCredit($paymentOrder);
        $webhooks = WebhookEvent::where('provider', 'phonepe')
            ->where('external_id', $paymentOrder->merchant_transaction_id)
            ->orderByDesc('created_at')
            ->get();

        $meta = $this->sanitizePayload($paymentOrder->meta ?? []);
        $latestWebhookPayload = $webhooks->first() ? $this->sanitizePayload($webhooks->first()->payload ?? []) : null;

        return view('admin.finance.payments.show', [
            'paymentOrder' => $paymentOrder,
            'walletCredit' => $walletCredit,
            'webhooks' => $webhooks,
            'meta' => $meta,
            'latestWebhookPayload' => $latestWebhookPayload,
        ]);
    }

    public function recheck(PaymentOrder $paymentOrder)
    {
        $before = $paymentOrder->only(['status', 'provider_transaction_id', 'amount']);
        CheckPhonePePaymentStatus::dispatch($paymentOrder->id, auth()->id());

        AdminActivityLogger::log('finance.payment.recheck_queued', null, [
            'payment_order_id' => $paymentOrder->id,
            'merchant_transaction_id' => $paymentOrder->merchant_transaction_id,
            'before' => $before,
        ]);

        return back()->with('success', 'Payment status recheck queued.');
    }

    public function retryWebhook(PaymentOrder $paymentOrder)
    {
        $event = WebhookEvent::where('provider', 'phonepe')
            ->where('external_id', $paymentOrder->merchant_transaction_id)
            ->orderByDesc('created_at')
            ->first();

        if (!$event) {
            return back()->with('error', 'No webhook event found for this payment.');
        }

        $prevStatus = $event->processing_status;
        $event->update([
            'processing_status' => 'pending',
            'error_message' => null,
        ]);

        ProcessPhonePeWebhook::dispatch($event->id);

        AdminActivityLogger::log('finance.payment.webhook_retry', null, [
            'payment_order_id' => $paymentOrder->id,
            'merchant_transaction_id' => $paymentOrder->merchant_transaction_id,
            'webhook_event_id' => $event->id,
            'previous_processing_status' => $prevStatus,
        ]);

        return back()->with('success', 'Webhook retry queued.');
    }

    public function updateNote(Request $request, PaymentOrder $paymentOrder)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:1000',
            'admin_note_status' => 'nullable|in:investigating,cleared',
        ]);

        $before = [
            'admin_note' => $paymentOrder->admin_note,
            'admin_note_status' => $paymentOrder->admin_note_status,
        ];

        $paymentOrder->update([
            'admin_note' => $validated['admin_note'] ?? null,
            'admin_note_status' => $validated['admin_note_status'] ?? null,
        ]);

        AdminActivityLogger::log('finance.payment.note_updated', null, [
            'payment_order_id' => $paymentOrder->id,
            'merchant_transaction_id' => $paymentOrder->merchant_transaction_id,
            'before' => $before,
            'after' => [
                'admin_note' => $paymentOrder->admin_note,
                'admin_note_status' => $paymentOrder->admin_note_status,
            ],
        ]);

        return back()->with('success', 'Admin note updated.');
    }

    protected function buildPaymentsQuery(Request $request)
    {
        $query = PaymentOrder::query()->with('user');

        $walletCreditSubquery = DB::table('wallet_transactions')
            ->selectRaw('COUNT(*)')
            ->where('wallet_transactions.type', 'credit')
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->where('wallet_transactions.reference_type', 'recharge')
                        ->whereColumn('wallet_transactions.reference_id', 'payment_orders.id');
                })->orWhereColumn('wallet_transactions.idempotency_key', 'payment_orders.merchant_transaction_id')
                    ->orWhereRaw("wallet_transactions.idempotency_key = CONCAT('phonepe:', payment_orders.merchant_transaction_id')");
            });

        $query->select('payment_orders.*')
            ->selectSub($walletCreditSubquery, 'wallet_credit_count');

        if ($request->filled('status')) {
            $query->whereIn('status', $this->statusFilterValues($request->status));
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            [$startUtc, $endUtc] = $this->parseDateRange($request);
            if ($startUtc) {
                $query->where('created_at', '>=', $startUtc);
            }
            if ($endUtc) {
                $query->where('created_at', '<=', $endUtc);
            }
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        if ($request->filled('merchant_transaction_id')) {
            $query->where('merchant_transaction_id', 'like', '%' . $request->merchant_transaction_id . '%');
        }

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }

    protected function statusOptions(): array
    {
        return [
            'initiated' => 'Initiated',
            'redirected' => 'Redirected',
            'pending' => 'Pending',
            'success' => 'Success',
            'failed' => 'Failed',
            'expired' => 'Expired',
        ];
    }

    protected function statusFilterValues(string $status): array
    {
        $normalized = strtolower($status);

        if ($normalized === 'success') {
            return ['success', 'completed', 'paid', 'PAID'];
        }

        return [$status];
    }

    protected function findWalletCredit(PaymentOrder $paymentOrder): ?WalletTransaction
    {
        return WalletTransaction::query()
            ->where('type', 'credit')
            ->where(function ($q) use ($paymentOrder) {
                $q->where(function ($inner) use ($paymentOrder) {
                    $inner->where('reference_type', 'recharge')
                        ->where('reference_id', $paymentOrder->id);
                })->orWhere('idempotency_key', $paymentOrder->merchant_transaction_id)
                    ->orWhere('idempotency_key', 'phonepe:' . $paymentOrder->merchant_transaction_id);
            })
            ->orderByDesc('created_at')
            ->first();
    }

    protected function parseDateRange(Request $request): array
    {
        $tz = 'Asia/Kolkata';
        $startUtc = null;
        $endUtc = null;

        if ($request->filled('start_date')) {
            $startUtc = Carbon::parse($request->start_date, $tz)->startOfDay()->setTimezone('UTC');
        }
        if ($request->filled('end_date')) {
            $endUtc = Carbon::parse($request->end_date, $tz)->endOfDay()->setTimezone('UTC');
        }

        return [$startUtc, $endUtc];
    }

    protected function filters(Request $request): array
    {
        return [
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'merchant_transaction_id' => $request->merchant_transaction_id,
            'user_search' => $request->user_search,
        ];
    }

    protected function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['signature', 'salt', 'secret', 'x-verify', 'checksum', 'token', 'key'];

        $clean = [];
        foreach ($payload as $key => $value) {
            $lower = strtolower((string) $key);
            $isSensitive = false;
            foreach ($sensitiveKeys as $needle) {
                if (str_contains($lower, $needle)) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $clean[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->sanitizePayload($value);
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}
