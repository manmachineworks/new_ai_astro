@extends('admin.layouts.app')

@section('title', 'Reviews Moderation')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-dark">Reviews Moderation</h2>
            <p class="text-muted mb-0">Hide, restore, or remove reviews.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="User or astrologer">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Astrologer</label>
                    <select name="astrologer_id" class="form-select">
                        <option value="">All</option>
                        @foreach($astrologers as $astro)
                            <option value="{{ $astro->id }}" @selected(request('astrologer_id') == $astro->id)>
                                {{ $astro->display_name ?? ('Astrologer #' . $astro->id) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <x-admin.table :columns="['Date', 'User', 'Astrologer', 'Rating', 'Comment', 'Status', 'Actions']" :rows="$reviews">
        @forelse($reviews as $review)
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark">{{ $review->created_at->format('M d, Y') }}</div>
                    <div class="small text-muted">{{ $review->created_at->format('H:i') }}</div>
                </td>
                <td>{{ $review->user?->name ?? 'User' }}</td>
                <td>{{ $review->astrologerProfile?->display_name ?? 'Astrologer' }}</td>
                <td><span class="badge bg-light text-dark border">{{ $review->rating }}</span></td>
                <td class="small text-muted">{{ $review->comment ?? '-' }}</td>
                <td>
                    <span class="badge bg-{{ $review->status === 'published' ? 'success' : 'secondary' }}">
                        {{ ucfirst($review->status) }}
                    </span>
                </td>
                <td class="text-end pe-4">
                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="{{ $review->status === 'published' ? 'hidden' : 'published' }}">
                        <button type="submit" class="btn btn-sm btn-light rounded-circle text-warning me-2"
                            data-bs-toggle="tooltip" title="Toggle Visibility">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                        data-confirm data-confirm-title="Delete Review"
                        data-confirm-text="Delete this review permanently?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light rounded-circle text-danger"
                            data-bs-toggle="tooltip" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="fas fa-star-half-alt fa-3x opacity-25"></i></div>
                    <p class="text-muted">No reviews found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection
