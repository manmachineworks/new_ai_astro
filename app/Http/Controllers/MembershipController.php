<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\PhonepePayment;
use App\Services\PhonePeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    protected $phonePeService;

    public function __construct(PhonePeService $phonePeService)
    {
        $this->phonePeService = $phonePeService;
    }

    public function index()
    {
        $plans = MembershipPlan::where('status', 'active')->orderBy('price_amount')->get();
        return view('memberships.index', compact('plans'));
    }

    public function checkout(Request $request, MembershipPlan $plan)
    {
        $user = $request->user();

        // Prevent duplicate active membership?
        // For now, allow upgrade/switch.

        $amount = $plan->price_amount;
        $merchantTxnId = 'TXN_' . Str::uuid()->toString();

        // Create Payment Record
        $payment = PhonepePayment::create([
            'user_id' => $user->id,
            'merchant_txn_id' => $merchantTxnId,
            'amount' => $amount,
            'status' => 'initiated',
            'type' => 'membership',
            'meta_json' => ['plan_id' => $plan->id, 'plan_name' => $plan->name]
        ]);

        // Call PhonePe API
        $response = $this->phonePeService->initiatePayment($merchantTxnId, (float) $amount, (string) $user->id, $user->phone);

        if ($response && isset($response['data']['instrumentResponse']['redirectInfo']['url'])) {
            $payment->update([
                'request_payload' => $response,
                'status' => 'pending'
            ]);

            return redirect($response['data']['instrumentResponse']['redirectInfo']['url']);
        }

        return back()->with('error', 'Payment initiation failed. Please try again.');
    }

    public function myMembership(Request $request)
    {
        $membership = $request->user()->activeMembership;
        $history = $request->user()->memberships()->with('plan')->latest()->get();
        return view('user.membership', compact('membership', 'history'));
    }
}
