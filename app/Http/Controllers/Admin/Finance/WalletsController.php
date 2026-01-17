<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\AdminActivityLogger;
use App\Services\CsvExportService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletsController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildLedgerQuery($request);
        $transactions = $query->latest('created_at')->paginate(25)->withQueryString();

        return view('admin.finance.wallets.index', [
            'transactions' => $transactions,
            'filters' => $this->filters($request),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function export(Request $request, CsvExportService $csv)
    {
        $query = $this->buildLedgerQuery($request)->orderBy('created_at');

        $cols = [
            'created_at' => 'Date (IST)',
            'user.name' => 'User',
            'user.email' => 'Email',
            'amount' => 'Amount',
            'type' => 'Type',
            'bucket' => 'Bucket',
            'source' => 'Source',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'balance_after' => 'Balance After',
            'description' => 'Description',
        ];

        return $csv->streamExport('finance_wallet_ledger.csv', $query, $cols);
    }

    public function show(Request $request, User $user)
    {
        $query = WalletTransaction::where('user_id', $user->id);

        if ($request->filled('type')) {
            $this->applyTypeFilter($query, $request->type);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
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

        $transactions = $query->latest('created_at')->paginate(20)->withQueryString();

        $bucketTotals = WalletTransaction::where('user_id', $user->id)
            ->select('bucket', DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE -ABS(amount) END) as balance"))
            ->groupBy('bucket')
            ->pluck('balance', 'bucket');

        return view('admin.finance.wallets.show', [
            'user' => $user,
            'transactions' => $transactions,
            'bucketTotals' => $bucketTotals,
            'filters' => $this->filters($request),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function adjust(Request $request, User $user, WalletService $walletService)
    {
        $validated = $request->validate([
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|min:5|max:255',
            'reference_id' => 'nullable|string|max:100',
            'idempotency_key' => 'required|string|size:36',
        ]);

        $existing = WalletTransaction::where('idempotency_key', $validated['idempotency_key'])->first();
        if ($existing) {
            return back()->with('error', 'This adjustment has already been processed.');
        }

        $beforeBalance = $user->wallet_balance;
        $txn = null;

        try {
            if ($validated['type'] === 'credit') {
                $txn = $walletService->credit(
                    $user,
                    $validated['amount'],
                    'admin_adjustment',
                    $validated['reference_id'] ?? null,
                    'Admin Credit: ' . $validated['reason'],
                    ['reason' => $validated['reason'], 'admin_id' => auth()->id()],
                    $validated['idempotency_key'],
                    'admin_adjustment'
                );
            } else {
                $txn = $walletService->debit(
                    $user,
                    $validated['amount'],
                    'admin_adjustment',
                    $validated['reference_id'] ?? null,
                    'Admin Debit: ' . $validated['reason'],
                    ['reason' => $validated['reason'], 'admin_id' => auth()->id()],
                    'admin_adjustment',
                    $validated['idempotency_key']
                );
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($txn) {
            $txn->created_by = auth()->id();
            $txn->save();
        }

        $afterBalance = $txn?->balance_after ?? $user->wallet_balance;

        AdminActivityLogger::log('finance.wallet.adjustment', $user, [
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'reference_id' => $validated['reference_id'] ?? null,
            'idempotency_key' => $validated['idempotency_key'],
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
        ]);

        return back()->with('success', 'Wallet adjustment completed.');
    }

    protected function buildLedgerQuery(Request $request)
    {
        $query = WalletTransaction::query()->with('user');

        if ($request->filled('user_search')) {
            $search = $request->user_search;
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $this->applyTypeFilter($query, $request->type);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
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

        return $query;
    }

    protected function applyTypeFilter($query, string $type): void
    {
        if (in_array($type, ['credit', 'debit'], true)) {
            $query->where('type', $type);
            return;
        }

        if ($type === 'refund') {
            $query->where('reference_type', 'like', '%refund%');
            return;
        }

        if ($type === 'hold') {
            $query->where('reference_type', 'like', '%hold%');
            return;
        }

        if ($type === 'release') {
            $query->where('reference_type', 'like', '%release%');
        }
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
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'source' => $request->source,
            'user_search' => $request->user_search,
        ];
    }

    protected function typeOptions(): array
    {
        return [
            'credit' => 'Credit',
            'debit' => 'Debit',
            'refund' => 'Refund',
            'hold' => 'Hold',
            'release' => 'Release',
        ];
    }
}
