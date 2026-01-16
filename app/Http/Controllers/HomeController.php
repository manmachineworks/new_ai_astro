<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch 3 random verified and visible astrologers
        $featuredAstrologers = User::role('Astrologer')
            ->whereHas('astrologerProfile', function ($query) {
                $query->where('verification_status', 'approved')
                    ->where('visibility', true);
            })
            ->with('astrologerProfile')
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('welcome', compact('featuredAstrologers'));
    }
}
