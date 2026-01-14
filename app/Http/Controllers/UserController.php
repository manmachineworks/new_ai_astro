<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user.dashboard', compact('user'));
    }

    public function wallet()
    {
        // Placeholder for wallet view
        $user = Auth::user();
        return view('user.dashboard', compact('user')); // Just reuse dashboard for now
    }
}
