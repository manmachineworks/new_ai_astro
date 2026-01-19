<?php

namespace App\Http\Controllers\Astrologer\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCallerDeskWebhook;
use Illuminate\Http\Request;

class CallerDeskWebhookController extends Controller
{
    public function handle(Request $request)
    {
        ProcessCallerDeskWebhook::dispatch($request->all(), $request->headers->all());
        return response()->json(['ok']);
    }
}
