<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsBannerController extends Controller
{
    public function index()
    {
        $banners = CmsBanner::orderBy('sort_order')->paginate(10);
        return view('admin.cms.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:2048', // 2MB
            'link_url' => 'nullable|url',
            'position' => 'required|string',
            'locale' => 'required|in:en,hi',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        CmsBanner::create($validated);

        return redirect()->route('admin.cms.banners.index')->with('success', 'Banner created.');
    }

    public function destroy(CmsBanner $banner)
    {
        // Delete image
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();
        return back()->with('success', 'Banner deleted.');
    }
}
