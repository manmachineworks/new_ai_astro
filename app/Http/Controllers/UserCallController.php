<?php

namespace App\Http\Controllers;

use App\Models\CallSession;
use Illuminate\Http\Request;

class UserCallController extends Controller
{
    public function index(Request $request)
    {
        $calls = $request->user()->callSessions()
            ->with('astrologerProfile')
            ->latest()
            ->paginate(15);

        return view('user.calls.index', compact('calls'));
    }
}
