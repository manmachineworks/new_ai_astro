<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AstrologerGridController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('Astrologer')
            ->whereHas('astrologerProfile', function ($q) {
                // Ensure profile exists and check optional visibility
                // $q->where('visibility', 'public'); 
            })
            ->with('astrologerProfile');

        // Search by Name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Skill (JSON column)
        if ($request->has('skill') && $request->skill != '') {
            $query->whereHas('astrologerProfile', function ($q) use ($request) {
                // JSON search for skill
                $q->whereJsonContains('skills', $request->skill);
            });
        }

        $astrologers = $query->paginate(12);

        $skills = ['Vedic', 'Nadi', 'Tarot', 'Numerology', 'Vastu', 'Psychic'];

        return view('astrologer.index', [
            'astrologers' => $astrologers,
            'skills' => $skills
        ]);
    }
}
