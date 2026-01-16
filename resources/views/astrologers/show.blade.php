@extends('layouts.app')

@section('title', $astrologer->display_name)

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Profile Header/Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0 cosmic-card">
                <div class="card-body text-center p-4">
                    <img src="{{ $astrologer->profile_photo_path ?? 'https://ui-avatars.com/api/?name='.urlencode($astrologer->display_name).'&background=6366f1&color=fff' }}" 
                         class="rounded-circle border border-4 border-white shadow mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h3 class="fw-bold">{{ $astrologer->display_name }}</h3>
                    <div class="text-muted mb-2">
                        @foreach($astrologer->languages ?? [] as $lang)
                            <span class="badge bg-light text-dark fw-normal border">{{ $lang }}</span>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center align-items-center mb-3">
                        <div class="bg-warning text-dark px-2 py-1 rounded me-2 fw-bold small">
                            <i class="fas fa-star"></i> {{ $astrologer->rating_avg }}
                        </div>
                        <span class="text-muted small">{{ $astrologer->rating_count }} Reviews</span>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg rounded-pill" 
                                {{ !$astrologer->is_call_enabled ? 'disabled' : '' }}
                                onclick="startGate('call')">
                            <i class="fas fa-phone-alt me-2"></i> Call (₹{{ (int)$astrologer->call_per_minute }}/min)
                        </button>
                        <button class="btn btn-primary btn-lg rounded-pill" 
                                {{ !$astrologer->is_chat_enabled ? 'disabled' : '' }}
                                onclick="startGate('chat')">
                            <i class="fas fa-comment-dots me-2"></i> Chat (₹{{ (int)$astrologer->chat_per_session }}/min)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Availability Widget -->
            <div class="card shadow-sm border-0 mt-3 p-3">
                <h6 class="fw-bold mb-3">Availability (UTC)</h6>
                <ul class="list-unstyled small">
                    @forelse($astrologer->availabilityRules as $rule)
                        <li class="d-flex justify-content-between mb-2 border-bottom pb-1">
                            <span class="fw-medium text-muted">
                                {{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$rule->day_of_week] }}
                            </span>
                            <span>
                                {{ \Carbon\Carbon::parse($rule->start_time_utc)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($rule->end_time_utc)->format('H:i') }}
                            </span>
                        </li>
                    @empty
                        <li class="text-muted fst-italic">No schedule set.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 section-title">About Me</h5>
                    <p class="text-muted" style="line-height: 1.8;">
                        {{ $astrologer->bio }}
                    </p>

                    <h6 class="fw-bold mt-4 mb-2">Expertise</h6>
                    <div class="mb-3">
                        @foreach($astrologer->skills ?? [] as $skill)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 me-2 mb-2 rounded-pill">{{ $skill }}</span>
                        @endforeach
                    </div>
                    
                    <h6 class="fw-bold mt-4 mb-2">Specialties</h6>
                    <div class="mb-3">
                        @foreach($astrologer->specialties ?? [] as $spec)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 me-2 mb-2 rounded-pill">{{ $spec }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 section-title">Client Reviews</h5>
                    
                    @forelse($astrologer->reviews as $review)
                        <div class="d-flex mb-4 border-bottom pb-3">
                            <div class="avatar-circle bg-light text-secondary me-3 flex-shrink-0">
                                {{ substr($review->user->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 me-2">{{ $review->user->name ?? 'Anonymous' }}</h6>
                                    <div class="text-warning small">
                                        @for($i=0; $i<$review->rating; $i++) <i class="fas fa-star"></i> @endfor
                                    </div>
                                </div>
                                <div class="text-muted small mb-2">{{ $review->created_at->format('M d, Y') }}</div>
                                <p class="mb-0 text-secondary">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            No reviews yet. Be the first to consult!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startGate(type) {
    if (!confirm('Start ' + type + ' session? Wallet gating check will occur.')) return;
    
    // In a real app, this would be an AJAX call to the gate endpoint
    // For now, we simulate the GATE CHECK logic via alert or mock
    
    fetch(`/api/astrologers/{{ $astrologer->id }}/gate/` + type, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if(data.error) {
            alert('Error: ' + data.message);
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert('Success! Token: ' + data.token);
            // Redirect to actual Call/Chat UI
        }
    })
    .catch(e => alert('System Error'));
}
</script>
@endsection
