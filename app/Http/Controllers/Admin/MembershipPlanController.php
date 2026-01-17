<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::latest()->get();
        return view('admin.memberships.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.memberships.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price_amount' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'benefits.call_discount_percent' => 'nullable|integer|min:0|max:100',
            'benefits.chat_discount_percent' => 'nullable|integer|min:0|max:100',
            'benefits.ai_free_messages' => 'nullable|integer|min:0',
            'benefits.priority_support' => 'nullable|boolean',
        ]);

        MembershipPlan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'price_amount' => $request->price_amount,
            'duration_days' => $request->duration_days,
            'benefits_json' => [
                'call_discount_percent' => $request->input('benefits.call_discount_percent', 0),
                'chat_discount_percent' => $request->input('benefits.chat_discount_percent', 0),
                'ai_free_messages' => $request->input('benefits.ai_free_messages', 0),
                'priority_support' => $request->input('benefits.priority_support', false),
            ],
            'status' => $request->status ?? 'active',
            'created_by_admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.memberships.plans.index')->with('success', 'Plan created successfully');
    }

    public function edit(MembershipPlan $plan)
    {
        return view('admin.memberships.plans.edit', compact('plan'));
    }

    public function update(Request $request, MembershipPlan $plan)
    {
        $request->validate([
            'name' => 'required',
            'price_amount' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'benefits.call_discount_percent' => 'nullable|integer|min:0|max:100',
            'benefits.chat_discount_percent' => 'nullable|integer|min:0|max:100',
            'benefits.ai_free_messages' => 'nullable|integer|min:0',
        ]);

        $plan->update([
            'name' => $request->name,
            'price_amount' => $request->price_amount,
            'duration_days' => $request->duration_days,
            'benefits_json' => [
                'call_discount_percent' => $request->input('benefits.call_discount_percent', 0),
                'chat_discount_percent' => $request->input('benefits.chat_discount_percent', 0),
                'ai_free_messages' => $request->input('benefits.ai_free_messages', 0),
                'priority_support' => $request->input('benefits.priority_support', false),
            ],
            'status' => $request->status,
        ]);

        return redirect()->route('admin.memberships.plans.index')->with('success', 'Plan updated successfully');
    }

    public function destroy(MembershipPlan $plan)
    {
        // Check for active subscriptions before deleting?
        // Ideally just deactivate.
        $plan->delete();
        return redirect()->route('admin.memberships.plans.index')->with('success', 'Plan deleted');
    }
}
