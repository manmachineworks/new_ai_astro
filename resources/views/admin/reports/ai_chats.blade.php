@extends('admin.layouts.app')

@section('title', 'AI Chat Sessions Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">AI Chat Sessions</h2>
            <div>
                <a href="{{ route('admin.reports.ai_chats', array_merge(request()->all(), ['export' => 1])) }}"
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
                            <th>Mode</th>
                            <th>Msgs</th>
                            <th>Gross</th>
                            <th>Commission</th>
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
                                    <span
                                        class="badge bg-light text-dark rounded-pill border">{{ str_replace('_', ' ', $session->pricing_mode) }}</span>
                                </td>
                                <td>{{ $session->total_messages }}</td>
                                <td class="fw-bold">₹{{ number_format($session->total_charged, 2) }}</td>
                                <td class="text-danger">₹{{ number_format($session->commission_amount_total, 2) }}</td>
                                <td class="pe-4 small">
                                    {{ $session->updated_at->setTimezone('Asia/Kolkata')->format('d M, h:i A') }}
                                </td>
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