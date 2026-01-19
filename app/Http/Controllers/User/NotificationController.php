<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = [
            [
                'id' => 1,
                'type' => 'recharge',
                'title' => 'Wallet Recharged Successfully',
                'message' => 'Your wallet has been credited with ₹500.00. Transaction ID: TXN789012',
                'time' => '2 hours ago',
                'read' => false,
                'action' => false
            ],
            [
                'id' => 2,
                'type' => 'call',
                'title' => 'Call Completed',
                'message' => 'You had a call with Astro Priya for 10 minutes.',
                'time' => 'Yesterday',
                'read' => true,
                'action' => true
            ],
            [
                'id' => 3,
                'type' => 'offer',
                'title' => '50% OFF on Tarot Reading',
                'message' => 'Limited time offer! Get 50% discount on your next session with Tarot Tina.',
                'time' => '2 days ago',
                'read' => true,
                'action' => true
            ],
            [
                'id' => 4,
                'type' => 'system',
                'title' => 'Welcome to AI Astro',
                'message' => 'Thanks for joining! Complete your profile to get a free personalized horoscope.',
                'time' => '1 week ago',
                'read' => true,
                'action' => false
            ],
        ];

        return view('user.notifications.index', compact('notifications'));
    }

    public function markAllRead(Request $request)
    {
        return back()->with('success', 'All notifications marked as read.');
    }
}
