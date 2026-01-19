<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\UpdatePricingRequest;
use App\Models\AstrologerPricing;
use App\Models\AstrologerPricingAudit;
use Inertia\Inertia;

class PricingController extends AstrologerBaseController
{
    public function edit()
    {
        $astrologer = $this->resolveAstrologer();
        $pricing = AstrologerPricing::firstOrCreate(['astrologer_id' => $astrologer->id]);

        return Inertia::render('Astrologer/Pricing', [
            'pricing' => $pricing,
        ]);
    }

    public function update(UpdatePricingRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        $pricing = AstrologerPricing::firstOrNew(['astrologer_id' => $astrologer->id]);

        $old = $pricing->getAttributes();
        $pricing->fill($request->validated());
        $pricing->save();

        AstrologerPricingAudit::create([
            'astrologer_id' => $astrologer->id,
            'changed_by_user_id' => $request->user()->id,
            'old_values' => $old ?: null,
            'new_values' => $pricing->getAttributes(),
        ]);

        return back()->with('success', 'Pricing updated and audit recorded.');
    }
}
