@extends('admin.layouts.app')

@section('title', 'Call Sessions Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Call Sessions</h2>
            <div>
                <a href="{{ route('admin.reports.calls', array_merge(request()->all(), ['export' => 1])) }}"
                    class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </a>
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-light rounded-pill px-4 ms-2">Back to
                    Dashboard</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>User</th>
                            <th>Astrologer</th>
                            <th>Duration</th>
                            <th>Gross</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th class="pe-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td class="ps-4"><span class="small text-muted">#{{ substr($session->id, 0, 8) }}</span></td>
                                <td>
                                    <div class="fw-bold">{{ $session->user->name }}</div>
                                    <div class="small text-muted">{{ $session->user->phone }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $session->astrologerProfile->user->name }}</div>
                                    <div class="small text-muted">Astro #{{ $session->astrologerProfile->id }}</div>
                                </td>
                                <td>{{ $session->billable_minutes ?? 0 }} mins</td>
                                <td class="fw-bold">₹{{ number_format($session->gross_amount, 2) }}</td>
                                <td class="text-danger">₹{{ number_format($session->platform_commission_amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $session->status == 'completed' ? 'success' : 'warning' }}-subtle text-{{ $session->status == 'completed' ? 'success' : 'warning' }} rounded-pill px-3">
                                        {{ strtoupper($session->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 small">
                                    {{ $session->updated_at->setTimezone('Asia/Kolkata')->format('d M, h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
@endsection