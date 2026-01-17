<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCampaign;
use App\Models\PromoRedemption;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    /**
     * List all promo campaigns
     */
    public function index(Request $request)
    {
        $query = PromoCampaign::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $campaigns = $query->withCount('redemptions')
            ->latest()
            ->paginate(20);

        return view('admin.promos.index', compact('campaigns'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.promos.create');
    }

    /**
     * Store new campaign
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promo_campaigns,code',
            'type' => 'required|in:coupon,cashback,referral,first_time',
            'discount_type' => 'required|in:flat,percent',
            'discount_value' => 'required|numeric|min:0',
            'applies_to' => 'required|array',
            'applies_to.*' => 'in:recharge,call,chat,ai_chat,appointment,all',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'min_recharge_amount' => 'nullable|numeric|min:0',
            'min_spend_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_at_utc' => 'nullable|date',
            'end_at_utc' => 'nullable|date|after:start_at_utc',
            'first_time_only' => 'boolean',
        ]);

        $validated['status'] = 'active';
        $validated['created_by_admin_id'] = $request->user()->id;

        PromoCampaign::create($validated);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo campaign created successfully');
    }

    /**
     * Show campaign details
     */
    public function show($id)
    {
        $campaign = PromoCampaign::withCount('redemptions')->findOrFail($id);

        $redemptions = PromoRedemption::where('promo_campaign_id', $id)
            ->with('user')
            ->where('status', 'applied')
            ->latest()
            ->paginate(50);

        $stats = [
            'total_redemptions' => $redemptions->total(),
            'total_discount' => PromoRedemption::where('promo_campaign_id', $id)
                ->where('status', 'applied')
                ->sum('discount_amount'),
            'total_bonus' => PromoRedemption::where('promo_campaign_id', $id)
                ->where('status', 'applied')
                ->sum('bonus_credited'),
        ];

        return view('admin.promos.show', compact('campaign', 'redemptions', 'stats'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $campaign = PromoCampaign::findOrFail($id);
        return view('admin.promos.edit', compact('campaign'));
    }

    /**
     * Update campaign
     */
    public function update(Request $request, $id)
    {
        $campaign = PromoCampaign::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promo_campaigns,code,' . $id,
            'type' => 'required|in:coupon,cashback,referral,first_time',
            'discount_type' => 'required|in:flat,percent',
            'discount_value' => 'required|numeric|min:0',
            'applies_to' => 'required|array',
            'applies_to.*' => 'in:recharge,call,chat,ai_chat,appointment,all',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'min_recharge_amount' => 'nullable|numeric|min:0',
            'min_spend_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_at_utc' => 'nullable|date',
            'end_at_utc' => 'nullable|date|after:start_at_utc',
            'first_time_only' => 'boolean',
        ]);

        $campaign->update($validated);

        return redirect()->route('admin.promos.show', $id)
            ->with('success', 'Promo campaign updated successfully');
    }

    /**
     * Toggle campaign status
     */
    public function toggle($id)
    {
        $campaign = PromoCampaign::findOrFail($id);

        $newStatus = $campaign->status === 'active' ? 'inactive' : 'active';
        $campaign->update(['status' => $newStatus]);

        return back()->with('success', 'Campaign status updated to ' . $newStatus);
    }

    /**
     * Delete campaign
     */
    public function destroy($id)
    {
        $campaign = PromoCampaign::findOrFail($id);

        // Check if has redemptions
        if ($campaign->redemptions()->exists()) {
            return back()->with('error', 'Cannot delete campaign with existing redemptions');
        }

        $campaign->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Campaign deleted successfully');
    }
}
