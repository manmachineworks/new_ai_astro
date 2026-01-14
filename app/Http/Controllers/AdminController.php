<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function users()
    {
        $users = User::where('role', 'user')->paginate(10);
        return view('admin.dashboard', compact('users')); // Placeholder
    }

    public function astrologers()
    {
        $astrologers = AstrologerProfile::with('user')->paginate(10);
        return view('admin.dashboard', compact('astrologers')); // Placeholder
    }
}
