<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\UpdateServicesRequest;
use App\Models\AstrologerService;
use Inertia\Inertia;

class ServiceController extends AstrologerBaseController
{
    public function edit()
    {
        $astrologer = $this->resolveAstrologer();
        $service = AstrologerService::firstOrCreate(['astrologer_id' => $astrologer->id]);

        return Inertia::render('Astrologer/Services', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServicesRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        $service = AstrologerService::updateOrCreate(
            ['astrologer_id' => $astrologer->id],
            $request->validated()
        );

        return back()->with('success', 'Service toggles updated.');
    }
}
