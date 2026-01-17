<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::paginate(10);
        return view('admin.blog.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:190',
            'locale' => 'required|in:en,hi',
        ]);

        // Manual unique check for slug+locale
        if (BlogCategory::where('slug', $validated['slug'])->where('locale', $validated['locale'])->exists()) {
            return back()->withErrors(['slug' => 'Slug already exists for this locale.']);
        }

        $validated['is_active'] = $request->has('is_active'); // Checkbox

        BlogCategory::create($validated);
        return back()->with('success', 'Category created.');
    }

    public function update(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:190',
            'locale' => 'required|in:en,hi',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'locale' => $validated['locale'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
