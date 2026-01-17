<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * List all referrals
     */
    public function index(Request $request)
    {
        $query = Referral::with(['inviter', 'invitee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('inviter_id')) {
            $query->where('inviter_user_id', $request->inviter_id);
        }

        $referrals = $query->latest()->paginate(50);

        // Summary stats
        $stats = [
            'total' => Referral::count(),
            'pending' => Referral::where('status', 'pending')->count(),
            'qualified' => Referral::where('status', 'qualified')->count(),
            'rewarded' => Referral::where('status', 'rewarded')->count(),
            'rejected' => Referral::where('status', 'rejected')->count(),
            'total_inviter_bonus' => Referral::where('status', 'rewarded')->sum('inviter_bonus_amount'),
            'total_invitee_bonus' => Referral::where('status', 'rewarded')->sum('invitee_bonus_amount'),
        ];

        return view('admin.referrals.index', compact('referrals', 'stats'));
    }

    /**
     * Show referral detail
     */
    public function show($id)
    {
        $referral = Referral::with(['inviter.referralCode', 'invitee'])->findOrFail($id);

        // Get invitee's payment history
        $inviteePayments = \App\Models\PaymentOrder::where('user_id', $referral->invitee_user_id)
            ->where('status', 'success')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.referrals.show', compact('referral', 'inviteePayments'));
    }

    /**
     * Override referral status (admin action)
     */
    public function override(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'nullable|string|max:500',
        ]);

        $referral = Referral::findOrFail($id);

        if ($request->action === 'approve') {
            if ($referral->status === 'pending') {
                try {
                    $this->referralService->processQualification($referral);
                    return back()->with('success', 'Referral approved and bonuses credited');
                } catch (\Exception $e) {
                    return back()->with('error', 'Failed to process referral: ' . $e->getMessage());
                }
            }
        } else {
            // Reject
            $referral->markRejected($request->reason);
            return back()->with('success', 'Referral rejected');
        }

        return back();
    }

    /**
     * Export referrals (CSV)
     */
    public function export(Request $request)
    {
        $query = Referral::with(['inviter', 'invitee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $referrals = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="referrals_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($referrals) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID',
                'Inviter ID',
                'Inviter Name',
                'Invitee ID',
                'Invitee Name',
                'Status',
                'Inviter Bonus',
                'Invitee Bonus',
                'Qualified At',
                'Rewarded At',
                'Created At',
            ]);

            // Data
            foreach ($referrals as $referral) {
                fputcsv($file, [
                    $referral->id,
                    $referral->inviter_user_id,
                    $referral->inviter->name ?? '',
                    $referral->invitee_user_id,
                    $referral->invitee->name ?? '',
                    $referral->status,
                    $referral->inviter_bonus_amount ?? 0,
                    $referral->invitee_bonus_amount ?? 0,
                    $referral->qualified_at?->toDateTimeString() ?? '',
                    $referral->rewarded_at?->toDateTimeString() ?? '',
                    $referral->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
