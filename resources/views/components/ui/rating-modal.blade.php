@props(['trigger' => 'openRatingModal'])

<div x-data="{ open: false, rating: 0, feedback: '' }" x-on:{{ $trigger }}.window="open = true; rating=0; feedback=''"
    class="modal fade" :class="{ 'show d-block': open }" style="background: rgba(0,0,0,0.5);" tabindex="-1"
    aria-modal="true" role="dialog" x-show="open">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" @click.away="open = false">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" @click="open = false" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center pt-0">
                <h5 class="modal-title fw-bold mb-3" id="modal-title">
                    How was your experience?
                </h5>

                {{-- Stars --}}
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <template x-for="i in 5">
                        <button @click="rating = i" class="btn btn-link p-0 text-decoration-none border-0">
                            <i class="bi fs-2"
                                :class="rating >= i ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"></i>
                        </button>
                    </template>
                </div>

                {{-- Feedback --}}
                <div class="mb-3">
                    <textarea x-model="feedback" rows="3" class="form-control"
                        placeholder="Write a review (optional)"></textarea>
                </div>
            </div>

            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-secondary" @click="open = false">Skip</button>
                <button type="button"
                    @click="open = false; $dispatch('rating-submitted', { rating: rating, feedback: feedback })"
                    class="btn btn-primary px-4">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>