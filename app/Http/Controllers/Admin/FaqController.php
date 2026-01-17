<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->paginate(15);
        return view('admin.cms.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer_html' => 'required|string',
            'locale' => 'required|in:en,hi',
            'sort_order' => 'integer',
        ]);

        Faq::create($validated);
        return back()->with('success', 'FAQ added.');
    }

    public function update(Request $request, Faq $faq)
    {
        // ... simplified for speed
        $faq->update($request->all());
        return back()->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted.');
    }
}
