@extends('admin.layouts.app')

@section('title', 'Manage Astrologers')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Astrologers</h5>
            <div>
                <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}"
                    class="btn btn-sm btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('admin.astrologers.index', ['status' => 'approved']) }}"
                    class="btn btn-sm btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('admin.astrologers.index') }}"
                    class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Astrologer</th>
                        <th>Status</th>
                        <th>Visibility</th>
                        <th>Account</th>
                        <th>Experience</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($astrologers as $user)
                        @php $profile = $user->astrologerProfile; @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Avatar -->
                                    <div class="avatar-circle bg-light text-primary me-2">
                                        {{ substr($profile->display_name ?? $user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $profile->display_name ?? $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($profile->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @elseif($profile->verification_status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($profile->show_on_front)
                                    <span class="badge bg-primary">Visible</span>
                                @else
                                    <span class="badge bg-light text-secondary border">Hidden</span>
                                @endif
                            </td>
                            <td>
                                @if($profile->is_enabled)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Disabled</span>
                                @endif
                            </td>
                            <td>
                                {{ $profile->experience_years }} Years
                            </td>
                            <td>
                                <a href="{{ route('admin.astrologers.show', $user->id) }}"
                                    class="btn btn-sm btn-outline-primary">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $astrologers->appends(request()->query())->links() }}
        </div>
    </div>
@endsection