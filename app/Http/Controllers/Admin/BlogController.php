<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('category')->latest()->paginate(10);
        return view('admin.blog.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::where('is_active', true)->get();
        return view('admin.blog.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug',
            'locale' => 'required|in:en,hi',
            'content_html' => 'required',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog', 'public');
            $validated['featured_image_path'] = $path;
        }

        $validated['author_admin_id'] = auth()->id();
        $validated['status'] = $request->has('publish') ? 'published' : 'draft';
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        // Tags logic (simple string comma separated or array?)
        // For now, skipping complex tag UI, just basic post.

        return redirect()->route('admin.blog.posts.index')->with('success', 'Post created.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::where('is_active', true)->get();
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,' . $post->id,
            'locale' => 'required|in:en,hi',
            'content_html' => 'required',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog', 'public');
            $validated['featured_image_path'] = $path;
        }

        $post->update($validated);

        return redirect()->route('admin.blog.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted.');
    }
}
