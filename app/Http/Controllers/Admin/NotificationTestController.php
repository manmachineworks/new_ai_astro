<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeviceToken;
use App\Services\FCMNotificationService;
use Illuminate\Http\Request;

class NotificationTestController extends Controller
{
    protected $fcmService;

    public function __construct(FCMNotificationService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index()
    {
        return view('admin.notifications.test'); // Simple form view
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:chat_message,call_incoming,wallet_low',
            'custom_body' => 'nullable|string|max:100'
        ]);

        $user = User::findOrFail($request->user_id);
        $type = $request->type;
        $body = $request->custom_body ?? "This is a test notification for {$type}.";

        // Logic based on types
        if ($type === 'chat_message') {
            $payload = $this->fcmService->buildPayload(
                $type,
                "New Message",
                $body,
                ['chat_id' => 'test_chat_1', 'sender_id' => '999'],
                'app://chat/test_chat_1',
                'chat_test',
                'chat_test'
            );
        } elseif ($type === 'call_incoming') {
            $payload = $this->fcmService->buildPayload(
                $type,
                "Incoming Call",
                "User is calling...",
                ['call_session_id' => 'test_call_1'],
                'app://calls/test_call_1'
            );
        } else {
            $payload = $this->fcmService->buildPayload(
                $type,
                "Wallet Alert",
                $body,
                ['balance' => '50.00'],
                'app://wallet'
            );
        }

        $result = $this->fcmService->sendToUser($user->id, $payload);

        return back()->with('success', "Notification dispatched! Success: {$result['success']}, Failed: {$result['failure']}");
    }
}
