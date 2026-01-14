<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Calculate earnings, calls, etc. here
        return view('astrologer.dashboard', compact('user'));
    }

    public function schedule()
    {
        return view('astrologer.dashboard'); // Placeholder
    }
}
