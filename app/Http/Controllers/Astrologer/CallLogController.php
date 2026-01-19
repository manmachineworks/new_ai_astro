<?php

namespace App\Http\Controllers\Astrologer;

use App\Models\CallLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CallLogController extends AstrologerBaseController
{
    public function index(Request $request)
    {
        $astrologer = $this->resolveAstrologer();
        $query = CallLog::where('astrologer_id', $astrologer->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $calls = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Astrologer/Calls/Index', [
            'calls' => $calls,
        ]);
    }

    public function show(CallLog $callLog)
    {
        $this->assertOwnership($callLog);

        return Inertia::render('Astrologer/Calls/Show', [
            'call' => $callLog,
        ]);
    }

    protected function assertOwnership(CallLog $callLog): void
    {
        $astrologer = $this->resolveAstrologer();
        abort_unless($callLog->astrologer_id === $astrologer->id, 403);
    }
}
