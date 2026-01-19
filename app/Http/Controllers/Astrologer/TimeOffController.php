<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Requests\Astrologer\StoreTimeOffRequest;
use App\Models\AstrologerTimeOff;

class TimeOffController extends AstrologerBaseController
{
    public function store(StoreTimeOffRequest $request)
    {
        $astrologer = $this->resolveAstrologer();
        AstrologerTimeOff::create(array_merge($request->validated(), [
            'astrologer_id' => $astrologer->id,
        ]));

        return back()->with('success', 'Time off added.');
    }

    public function destroy(AstrologerTimeOff $timeOff)
    {
        $this->assertOwnTimeOff($timeOff);
        $timeOff->delete();

        return back()->with('success', 'Time off removed.');
    }

    protected function assertOwnTimeOff(AstrologerTimeOff $timeOff): void
    {
        $astrologer = $this->resolveAstrologer();
        abort_unless($timeOff->astrologer_id === $astrologer->id, 403);
    }
}
