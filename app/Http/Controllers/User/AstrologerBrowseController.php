<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerProfile;
use Illuminate\Http\Request;

class AstrologerBrowseController extends Controller
{
    public function index(Request $request)
    {
        $query = AstrologerProfile::with('user')
            ->where('verification_status', 'verified')
            ->where('visibility', true)
            ->where('is_enabled', true);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('speciality') && $request->speciality !== 'all') {
            // Assuming specialties is a JSON column.
            // SQLite/MySQL JSON handling might differ, using standard 'like' for broader compatibility if simple array stored as string, or JSON_CONTAINS
            // For now, simple LIKE check if simple array string [ "Vedic", "Tarot" ]
            $query->where('specialties', 'like', '%"' . $request->speciality . '"%');
        }

        if ($request->filled('language') && $request->language !== 'all') {
            $query->where('languages', 'like', '%"' . $request->language . '"%');
        }

        // Sorting
        $sort = $request->input('sort', 'popular');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('call_per_minute', 'asc');
                break;
            case 'price_high':
                $query->orderBy('call_per_minute', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('rating_avg');
                break;
            case 'experience':
                $query->orderByDesc('experience_years');
                break;
            default: // popular
                $query->orderByDesc('rating_avg')->orderByDesc('experience_years');
                break;
        }

        // Pagination
        $astrologers = $query->paginate(12)->withQueryString();

        // Transform collection to match View expectation if necessary, 
        // OR update View to use Model attributes directly. 
        // Best practice: Update View to use Model. I will assume View adaptation or loose accessors.
        // But for safety to match the 'mock' structure the view expects, let's map it or ensure view handles it.
        // The view 'user.astrologers.index' likely accesses $astro['name'], so array access or object access works.
        // Eloquent models support array access mostly.

        return view('user.astrologers.index', [
            'astrologers' => $astrologers,
            'filters' => $request->all()
        ]);
    }

    public function show($id)
    {
        $astrologer = AstrologerProfile::with([
            'user',
            'reviews' => function ($q) {
                $q->latest()->limit(5); // Latest 5 reviews
            }
        ])
            ->where('id', $id)
            ->where('verification_status', 'verified')
            ->firstOrFail();

        // Ensure we pass data in specific structure if view expects simple array keys 
        // that differ from column names (e.g. 'name' vs 'display_name')
        // Ideally we refactor View to use $astrologer->display_name. 
        // But let's check if we can pass model directly. 
        // Usually views are $astrologer['name']. Eloquent model $astrologer->name works too if accessor exists.

        return view('user.astrologers.show', compact('astrologer'));
    }
}
