<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::latest()->paginate(10);
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255', // Unique check validation handled carefully below
            'locale' => 'required|in:en,hi',
            'content_html' => 'required',
        ]);

        // Custom Unique Check for slug+locale
        if (CmsPage::where('slug', $validated['slug'])->where('locale', $validated['locale'])->exists()) {
            return back()->withErrors(['slug' => 'The slug has already been taken for this locale.']);
        }

        $validated['created_by_admin_id'] = auth()->id();
        $validated['status'] = $request->has('publish') ? 'published' : 'draft';
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        CmsPage::create($validated);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(CmsPage $page)
    {
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'locale' => 'required|in:en,hi',
            'content_html' => 'required',
        ]);

        // Custom Unique Check for slug+locale excluding current
        if (
            CmsPage::where('slug', $validated['slug'])
                ->where('locale', $validated['locale'])
                ->where('id', '!=', $page->id)
                ->exists()
        ) {
            return back()->withErrors(['slug' => 'The slug has already been taken for this locale.']);
        }

        $status = $request->has('publish') ? 'published' : 'draft';
        if ($status === 'published' && $page->status !== 'published') {
            $validated['published_at'] = now();
        }
        $validated['status'] = $status;

        $page->update($validated);

        return redirect()->route('admin.cms.pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted successfully.');
    }
}
