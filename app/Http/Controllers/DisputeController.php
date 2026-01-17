<?php

namespace App\Http\Controllers;

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
     * List user's disputes
     */
    public function index(Request $request)
    {
        $disputes = Dispute::where('user_id', $request->user()->id)
            ->with('refund')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $disputes->through(function ($dispute) {
                return [
                    'id' => $dispute->id,
                    'reference_type' => class_basename($dispute->reference_type),
                    'reference_id' => $dispute->reference_id,
                    'reason_code' => $dispute->reason_code,
                    'status' => $dispute->status,
                    'requested_amount' => $dispute->requested_refund_amount,
                    'approved_amount' => $dispute->approved_refund_amount,
                    'refund_issued' => $dispute->refund ? true : false,
                    'created_at' => $dispute->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    /**
     * Check eligibility for dispute
     */
    public function checkEligibility(Request $request)
    {
        $validated = $request->validate([
            'reference_type' => 'required|string',
            'reference_id' => 'required|string',
        ]);

        // Map string to class
        $typeMap = [
            'call' => \App\Models\CallSession::class,
            'chat' => \App\Models\ChatSession::class,
            'ai_chat' => \App\Models\AiChatSession::class,
            'appointment' => \App\Models\Appointment::class,
            'payment' => \App\Models\PaymentOrder::class,
        ];

        $referenceType = $typeMap[$validated['reference_type']] ?? null;
        if (!$referenceType) {
            return response()->json(['eligible' => false, 'reason' => 'Invalid reference type'], 400);
        }

        // Find reference
        $reference = $referenceType::find($validated['reference_id']);
        if (!$reference) {
            return response()->json(['eligible' => false, 'reason' => 'Transaction not found'], 404);
        }

        // Check eligibility
        $eligibility = $this->disputeService->checkEligibility($reference, $request->user());

        return response()->json($eligibility);
    }

    /**
     * Create new dispute
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_type' => 'required|string',
            'reference_id' => 'required|string',
            'reason_code' => 'required|in:poor_quality,technical_issue,no_service,overcharged,other',
            'description' => 'nullable|string|max:1000',
            'requested_refund_amount' => 'nullable|numeric|min:0',
        ]);

        // Map and find reference
        $typeMap = [
            'call' => \App\Models\CallSession::class,
            'chat' => \App\Models\ChatSession::class,
            'ai_chat' => \App\Models\AiChatSession::class,
            'appointment' => \App\Models\Appointment::class,
            'payment' => \App\Models\PaymentOrder::class,
        ];

        $referenceType = $typeMap[$validated['reference_type']] ?? null;
        if (!$referenceType) {
            return response()->json(['error' => 'Invalid reference type'], 400);
        }

        $reference = $referenceType::find($validated['reference_id']);
        if (!$reference) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        try {
            $dispute = $this->disputeService->createDispute(
                $request->user(),
                $reference,
                $validated['reason_code'],
                $validated['description'] ?? null,
                $validated['requested_refund_amount'] ?? null
            );

            return response()->json([
                'dispute_id' => $dispute->id,
                'status' => $dispute->status,
                'message' => 'Dispute created successfully. We will review it shortly.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * View dispute detail
     */
    public function show(Request $request, string $id)
    {
        $dispute = Dispute::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['events', 'refund'])
            ->firstOrFail();

        return response()->json([
            'id' => $dispute->id,
            'reference_type' => class_basename($dispute->reference_type),
            'reference_id' => $dispute->reference_id,
            'reason_code' => $dispute->reason_code,
            'description' => $dispute->description,
            'status' => $dispute->status,
            'requested_amount' => $dispute->requested_refund_amount,
            'approved_amount' => $dispute->approved_refund_amount,
            'admin_notes' => $dispute->admin_notes,
            'created_at' => $dispute->created_at->toIso8601String(),
            'refund' => $dispute->refund ? [
                'id' => $dispute->refund->id,
                'amount' => $dispute->refund->amount,
                'status' => $dispute->refund->status,
                'issued_at' => $dispute->refund->created_at->toIso8601String(),
            ] : null,
            'timeline' => $dispute->events->map(function ($event) {
                return [
                    'event_type' => $event->event_type,
                    'actor_type' => $event->actor_type,
                    'created_at' => $event->created_at->toIso8601String(),
                    'meta' => $event->meta_json,
                ];
            }),
        ]);
    }
}
