@extends('admin.layouts.app')

@section('title', 'Astrologer Details')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span
                            class="avatar-circle bg-primary text-white fs-3 mx-auto d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            {{ substr($astrologer->astrologerProfile->display_name ?? $astrologer->name, 0, 1) }}
                        </span>
                    </div>
                    <h5 class="fw-bold">{{ $astrologer->astrologerProfile->display_name ?? $astrologer->name }}</h5>
                    <p class="text-muted small">{{ $astrologer->email }}</p>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status</span>
                        @if($astrologer->astrologerProfile->is_verified)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span
                                class="badge bg-warning text-dark">{{ ucfirst($astrologer->astrologerProfile->verification_status) }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Frontend</span>
                        @if($astrologer->astrologerProfile->show_on_front)
                            <span class="badge bg-primary">Visible</span>
                        @else
                            <span class="badge bg-light text-secondary border">Hidden</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        <!-- Toggle Account -->
                        <form action="{{ route('admin.astrologers.toggleAccount', $astrologer->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            @if($astrologer->astrologerProfile->is_enabled)
                                <button class="btn btn-outline-danger btn-sm w-100 mb-2">Disable Account</button>
                            @else
                                <button class="btn btn-outline-success btn-sm w-100 mb-2">Enable Account</button>
                            @endif
                        </form>

                        <!-- Toggle Visibility -->
                        <form action="{{ route('admin.astrologers.toggleVisibility', $astrologer->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('PUT')
                            @if($astrologer->astrologerProfile->show_on_front)
                                <button class="btn btn-outline-secondary btn-sm w-100">Hide from Frontend</button>
                            @else
                                <button class="btn btn-outline-primary btn-sm w-100">Show on Frontend</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Verification Actions -->
            @if(!$astrologer->astrologerProfile->is_verified)
                <div class="card shadow-sm border-warning mb-4">
                    <div class="card-header bg-warning text-dark fw-bold">
                        Verification Required
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.astrologers.verify', $astrologer->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Review Actions</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="status" value="approved" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i> Approve & Verify
                                    </button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('rejectSection').classList.remove('d-none')">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </div>
                            </div>

                            <div id="rejectSection" class="d-none mt-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control mb-2" rows="2"
                                    placeholder="Explain why..."></textarea>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Confirm
                                    Rejection</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Details Tabs -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#docs">Documents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#schedule">Availability</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="about">
                        <h6 class="fw-bold">Bio</h6>
                        <p class="text-muted">{{ $astrologer->astrologerProfile->bio }}</p>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold">Languages</h6>
                                <p>{{ implode(', ', $astrologer->astrologerProfile->languages ?? []) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold">Skills</h6>
                                <p>{{ implode(', ', $astrologer->astrologerProfile->skills ?? []) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="docs">
                        <h6 class="fw-bold mb-3">Submitted Documents</h6>
                        @forelse($astrologer->astrologerProfile->documents as $doc)
                            <div class="d-flex align-items-center justify-content-between border p-2 rounded mb-2">
                                <div>
                                    <div class="fw-medium">{{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}</div>
                                    <div class="small text-muted">{{ $doc->created_at->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <a href="#" class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted fst-italic">No documents uploaded.</div>
                        @endforelse
                    </div>

                    <div class="tab-pane fade" id="schedule">
                        <h6 class="fw-bold mb-3">Weekly Schedule (UTC)</h6>
                        <ul class="list-group list-group-flush">
                            @forelse($astrologer->astrologerProfile->availabilityRules as $rule)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$rule->day_of_week] }}</span>
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($rule->start_time_utc)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($rule->end_time_utc)->format('H:i') }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No active schedule.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection