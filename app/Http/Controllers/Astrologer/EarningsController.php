<?php

namespace App\Http\Controllers\Astrologer;

use App\Models\Earning;
use Inertia\Inertia;

class EarningsController extends AstrologerBaseController
{
    public function index()
    {
        $astrologer = $this->resolveAstrologer();
        $earnings = Earning::where('astrologer_id', $astrologer->id)
            ->latest()
            ->paginate(12);

        return Inertia::render('Astrologer/Earnings', [
            'earnings' => $earnings,
        ]);
    }
}
