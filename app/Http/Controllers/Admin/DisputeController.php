<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Services\DisputeService;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    protected $disputeService;

    public function __construct(DisputeService $disputeService)
    {
        $this->disputeService = $disputeService;
    }

    /**
     * List all disputes with filters
     */
    public function index(Request $request)
    {
        $query = Dispute::with(['user', 'refund']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('reason_code')) {
            $query->where('reason_code', $request->reason_code);
        }

        $disputes = $query->latest()->paginate(50);

        // Summary stats
        $stats = [
            'pending' => Dispute::pending()->count(),
            'approved' => Dispute::whereIn('status', ['approved_full', 'approved_partial'])->count(),
            'rejected' => Dispute::where('status', 'rejected')->count(),
            'total_refunded' => \App\Models\Refund::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.disputes.index', compact('disputes', 'stats'));
    }

    /**
     * View dispute detail with reference data
     */
    public function show(string $id)
    {
        $dispute = Dispute::with(['user', 'events', 'refund'])->findOrFail($id);

        $transactionDetails = $dispute->getTransactionDetails();

        return view('admin.disputes.show', compact('dispute', 'transactionDetails'));
    }

    /**
     * Request more information from user
     */
    public function requestInfo(Request $request, string $id)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $dispute = Dispute::findOrFail($id);

        $this->disputeService->requestMoreInfo(
            $dispute,
            $request->user(),
            $validated['message']
        );

        return back()->with('success', 'Information requested from user');
    }

    /**
     * Approve refund (full or partial)
     */
    public function approve(Request $request, string $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        $dispute = Dispute::findOrFail($id);

        try {
            $refund = $this->disputeService->approveRefund(
                $dispute,
                $request->user(),
                $validated['amount'],
                $validated['reason']
            );

            return back()->with('success', 'Refund of ₹' . $refund->amount . ' approved and issued to user wallet');
        } catch (\Exception $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Reject dispute
     */
    public function reject(Request $request, string $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $dispute = Dispute::findOrFail($id);

        $this->disputeService->rejectDispute(
            $dispute,
            $request->user(),
            $validated['reason']
        );

        return back()->with('success', 'Dispute rejected');
    }
}
