@extends('admin.layouts.app')

@section('title', 'Astrologer Profile: ' . ($astrologer->astrologerProfile->display_name ?? $astrologer->name))
@section('page_title', 'Astrologer Detail')

@section('content')
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @php $profile = $astrologer->astrologerProfile; @endphp

                    <div class="avatar-circle mx-auto bg-primary text-white d-flex align-items-center justify-content-center mb-3"
                        style="width: 100px; height: 100px; border-radius: 50%; background-image: url('{{ $profile->profile_photo_path ? asset($profile->profile_photo_path) : '' }}'); background-size: cover;">
                        {{ !$profile->profile_photo_path ? strtoupper(substr($profile->display_name ?? $astrologer->name, 0, 1)) : '' }}
                    </div>

                    <h4>{{ $profile->display_name ?? $astrologer->name }}</h4>
                    <p class="text-muted">{{ $astrologer->email }}</p>

                    <div
                        class="alert alert-{{ $profile->verification_status == 'approved' ? 'success' : ($profile->verification_status == 'pending' ? 'warning' : 'danger') }} mb-3">
                        <strong>Status: {{ ucfirst($profile->verification_status) }}</strong>
                    </div>

                    @if($profile->verification_status == 'pending')
                        <div class="d-grid gap-2 mb-3">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Approve
                                Astrologer</button>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject
                                Application</button>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Experience</span>
                        <span>{{ $profile->experience_years }} Years</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Wallet Balance</span>
                        <span class="fw-bold">{{ number_format($astrologer->wallet_balance, 2) }}</span>
                    </div>

                    <hr>

                    <form action="{{ route('admin.astrologers.toggleAccount', $astrologer->id) }}" method="POST"
                        class="d-grid mb-2">
                        @csrf
                        @method('PUT')
                        <button
                            class="btn btn-outline-secondary">{{ $profile->is_enabled ? 'Disable Account' : 'Enable Account' }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Profile Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Profile Details</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small text-muted">Specialties</label>
                            <div>
                                @if($profile->specialties)
                                    @foreach($profile->specialties as $spec)
                                        <span class="badge bg-light text-dark border">{{ $spec }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted">Languages</label>
                            <div>
                                @if($profile->languages)
                                    @foreach($profile->languages as $lang)
                                        <span class="badge bg-light text-dark border">{{ $lang }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted">Bio</label>
                        <p class="mb-0">{{ $profile->bio }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Verification Documents</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profile->documents as $doc)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $doc->status == 'approved' ? 'success' : 'secondary' }}">{{ ucfirst($doc->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary"
                                            onclick="alert('Document preview implementation pending storage setup')">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No documents uploaded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.astrologers.verify', $astrologer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Astrologer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve <strong>{{ $profile->display_name }}</strong>?</p>
                        <p class="text-muted small">This will mark them as verified and allow them to take calls/chats if
                            enabled.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.astrologers.verify', $astrologer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection