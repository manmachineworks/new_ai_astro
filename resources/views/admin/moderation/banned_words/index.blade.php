@extends('admin.layouts.app')

@section('title', 'Banned Words')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Banned Words</h2>
            <p class="text-muted mb-0">Manage blocked terms for chat moderation.</p>
        </div>
    </div>

    @unless($tableExists ?? true)
        <div class="alert alert-warning mb-4">
            <strong>Notice:</strong> Moderation data tables are missing. Run the latest migrations (`php artisan migrate`) before using this section.
        </div>
    @endunless

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.moderation.banned_words.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Word">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.moderation.banned_words.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($tableExists)
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white fw-bold">Add New Word</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.moderation.banned_words.store') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Word or Phrase</label>
                        <input type="text" name="word" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-danger w-100">Add to List</button>
                    </div>
                </form>
            </div>
        </div>

        <x-admin.table :columns="['Word', 'Status', 'Added', 'Actions']" :rows="$words">
            @forelse($words as $word)
                <tr>
                    <td class="ps-4 fw-bold text-dark">{{ $word->word }}</td>
                <td>
                    <span class="badge bg-{{ $word->is_active ? 'success' : 'secondary' }}">
                        {{ $word->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="small text-muted">{{ $word->created_at->format('M d, Y') }}</td>
                <td class="text-end pe-4">
                    <form action="{{ route('admin.moderation.banned_words.toggle', $word) }}" method="POST" class="d-inline"
                        data-confirm data-confirm-title="Toggle Word"
                        data-confirm-text="Toggle status for this word?">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light rounded-circle text-warning me-2" title="Toggle">
                            <i class="fas fa-toggle-on"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.moderation.banned_words.destroy', $word) }}" method="POST" class="d-inline"
                        data-confirm data-confirm-title="Remove Word"
                        data-confirm-text="Remove this word from the banned list?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-ban fa-3x opacity-25"></i></div>
                    <p class="text-muted">No banned words defined.</p>
                </td>
            </tr>
        @endforelse
        </x-admin.table>
    @else
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body text-muted text-center py-5">
                Chat moderation tables are not available. Run migrations or seed the chat tables to continue.
            </div>
        </div>
    @endif
@endsection
