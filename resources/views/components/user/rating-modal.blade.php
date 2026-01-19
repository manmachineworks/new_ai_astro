@props(['show' => false, 'callId'])

<x-ui.modal :show="$show" name="rating-modal-{{ $callId }}" title="Rate your experience">
    <form action="#" method="POST" x-data="{ rating: 0 }">
        @csrf
        <div class="d-flex justify-content-center gap-2 my-4">
            @foreach(range(1, 5) as $star)
                <button type="button" @click="rating = {{ $star }}" class="btn p-0 border-0"
                    :class="rating >= {{ $star }} ? 'text-warning' : 'text-muted'" style="transition: all 0.2s ease;">
                    <i class="bi" :class="rating >= {{ $star }} ? 'bi-star-fill' : 'bi-star'"
                        style="font-size: 2.5rem;"></i>
                </button>
            @endforeach
        </div>

        <div class="mb-4">
            <label for="review" class="form-label small fw-medium text-muted">Review</label>
            <textarea id="review" name="review" rows="3" class="form-control"
                placeholder="How was your session? (Optional)"></textarea>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                Submit Feedback
            </button>
        </div>
    </form>
</x-ui.modal>