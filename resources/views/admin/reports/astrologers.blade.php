@extends('admin.layouts.app')

@section('title', 'Astrologer Performance')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Astrologer Performance</h2>
            <div>
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-light rounded-pill px-4 ms-2">Back to
                    Dashboard</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small fw-bold mb-2">Sort By</h6>
                <div class="btn-group">
                    <a href="{{ route('admin.reports.astrologers', ['sort' => 'calls_count', 'direction' => 'desc']) }}"
                        class="btn btn-sm btn-outline-secondary {{ request('sort') == 'calls_count' ? 'active' : '' }}">Most
                        Calls</a>
                    <a href="{{ route('admin.reports.astrologers', ['sort' => 'chats_count', 'direction' => 'desc']) }}"
                        class="btn btn-sm btn-outline-secondary {{ request('sort') == 'chats_count' ? 'active' : '' }}">Most
                        Chats</a>
                    <a href="{{ route('admin.reports.astrologers', ['sort' => 'calls_revenue', 'direction' => 'desc']) }}"
                        class="btn btn-sm btn-outline-secondary {{ request('sort') == 'calls_revenue' ? 'active' : '' }}">Highest
                        Call Revenue</a>
                    <a href="{{ route('admin.reports.astrologers', ['sort' => 'chats_revenue', 'direction' => 'desc']) }}"
                        class="btn btn-sm btn-outline-secondary {{ request('sort') == 'chats_revenue' ? 'active' : '' }}">Highest
                        Chat Revenue</a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Rank</th>
                                <th>Astrologer</th>
                                <th>Total Calls</th>
                                <th>Total Chats</th>
                                <th>Call Revenue</th>
                                <th>Chat Revenue</th>
                                <th class="pe-4 text-end">Total Generated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profiles as $index => $profile)
                                <tr>
                                    <td class="ps-4 text-muted">#{{ $profiles->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2 bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle"
                                                style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $profile->user->name }}</div>
                                                <!-- <div class="small text-muted">{{ $profile->specialties ?? 'Astrologer' }}</div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $profile->calls_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $profile->chats_count }}</span>
                                    </td>
                                    <td>INR {{ number_format($profile->calls_revenue ?? 0, 2) }}</td>
                                    <td>INR {{ number_format($profile->chats_revenue ?? 0, 2) }}</td>
                                    <td class="pe-4 text-end fw-bold text-success">
                                        INR {{ number_format(($profile->calls_revenue ?? 0) + ($profile->chats_revenue ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $profiles->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection


