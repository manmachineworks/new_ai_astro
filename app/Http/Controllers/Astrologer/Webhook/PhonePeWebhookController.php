<?php

namespace App\Http\Controllers\Astrologer\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPhonePeWebhook;
use Illuminate\Http\Request;

class PhonePeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        ProcessPhonePeWebhook::dispatch($request->all(), $request->headers->all());
        return response()->json(['ok']);
    }
}
