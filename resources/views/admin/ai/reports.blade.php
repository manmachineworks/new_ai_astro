@extends('layouts.admin')

@section('title', 'AI Chat Reports')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">AI Response Reports</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Reason</th>
                            <th>AI Message Content</th>
                            <th>Reported At</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $report->user->name }}</div>
                                    <div class="small text-muted">ID: {{ $report->user->id }}</div>
                                </td>
                                <td><span class="badge bg-warning text-dark">{{ $report->reason }}</span></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;"
                                        title="{{ $report->message->content }}">
                                        {{ $report->message->content }}
                                    </div>
                                </td>
                                <td>{{ $report->created_at->format('d M Y, h:i A') }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#reportModal{{ $report->id }}">
                                        View Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Details Modal -->
                            <div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold">Report Details #{{ $report->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small text-uppercase">Reason</label>
                                                <div class="p-2 bg-light rounded">{{ $report->reason }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small text-uppercase">Details</label>
                                                <div class="p-2 border rounded">
                                                    {{ $report->details ?? 'No additional details provided.' }}</div>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="fw-bold text-muted small text-uppercase">Flagged AI
                                                    Content</label>
                                                <div class="p-3 bg-light border-start border-primary border-4 rounded">
                                                    {{ $report->message->content }}
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                Session ID: {{ $report->message->ai_chat_session_id }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-flag fs-1 text-muted d-block mb-3"></i>
                                    <span class="text-muted">No reports found.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($reports->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection