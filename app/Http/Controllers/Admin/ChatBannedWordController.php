<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatBannedWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ChatBannedWordController extends Controller
{
    private const TABLE = 'chat_banned_words';

    public function index(Request $request)
    {
        $tableExists = Schema::hasTable(self::TABLE);
        $words = collect();

        if ($tableExists) {
            $query = ChatBannedWord::query();

            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            if ($search = $request->input('search')) {
                $query->where('word', 'like', "%{$search}%");
            }

            $words = $query->orderBy('word')->paginate(50)->withQueryString();
        }

        return view('admin.moderation.banned_words.index', compact('words', 'tableExists'));
    }

    public function store(Request $request)
    {
        $this->ensureTable();

        $validated = $request->validate([
            'word' => 'required|string|max:120|unique:chat_banned_words,word',
        ]);

        ChatBannedWord::create([
            'word' => strtolower(trim($validated['word'])),
            'is_active' => true,
            'created_by_admin_id' => auth()->id(),
        ]);

        return back()->with('success', 'Banned word added.');
    }

    public function toggle(ChatBannedWord $word)
    {
        $this->ensureTable();

        $word->update(['is_active' => !$word->is_active]);

        return back()->with('success', 'Banned word status updated.');
    }

    public function destroy(ChatBannedWord $word)
    {
        $this->ensureTable();

        $word->delete();

        return back()->with('success', 'Banned word removed.');
    }

    private function ensureTable(): void
    {
        if (!Schema::hasTable(self::TABLE)) {
            abort(404, 'Chat moderation tables are not migrated yet.');
        }
    }
}
