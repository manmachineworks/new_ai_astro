@extends('admin.layouts.app')

@section('title', 'Webhook Events')

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('admin.system.webhooks.index', ['filter' => 'dead_letter']) }}" class="text-decoration-none">
                <div class="card shadow-sm border-start-danger h-100 rounded-4">
                    <div class="card-body">
                        <div class="text-danger small text-uppercase fw-bold">Dead Letters</div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 fw-bold text-dark">
                                {{ \App\Models\WebhookEvent::where('processing_status', 'failed')->where('attempts', '>=', 3)->count() }}
                            </h3>
                            <div class="avatar-circle-sm bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px;"><i class="fas fa-bug"></i></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <x-admin.filter-bar :action="route('admin.system.webhooks.index')" :filters="['date', 'search']">
        <div class="col-md-2">
            <select name="status" class="form-select bg-light border-0">
                <option value="">Status</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="provider" class="form-select bg-light border-0">
                <option value="">Provider</option>
                <option value="razorpay" {{ request('provider') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                <option value="phonepe" {{ request('provider') == 'phonepe' ? 'selected' : '' }}>PhonePe</option>
            </select>
        </div>
    </x-admin.filter-bar>

    <x-admin.table :columns="['Date', 'Provider', 'Event', 'Status', 'Attempts', 'Last Error', 'Actions']" :rows="$events">
        @forelse($events as $event)
            <tr>
                <td class="ps-4">
                    <div class="fw-bold text-dark">{{ $event->created_at->format('M d, H:i') }}</div>
                    <div class="small text-muted">{{ $event->created_at->diffForHumans() }}</div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">{{ ucfirst($event->provider) }}</span>
                </td>
                <td>
                    <div class="font-monospace small text-primary">{{ $event->event_type }}</div>
                </td>
                <td>
                    @if($event->processing_status === 'completed')
                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Success</span>
                    @elseif($event->processing_status === 'failed')
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Failed</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Pending</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-secondary border rounded-pill">{{ $event->attempts }}</span>
                </td>
                <td>
                    <div class="text-truncate text-danger small" style="max-width: 200px;" title="{{ $event->error_message }}">
                        {{ $event->error_message ?? '-' }}
                    </div>
                </td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <a href="{{ route('admin.system.webhooks.show', $event->id) }}"
                            class="btn btn-sm btn-light rounded-start" title="View">
                            <i class="fas fa-eye text-primary"></i>
                        </a>
                        @if($event->processing_status === 'failed' || $event->processing_status === 'pending')
                            <form action="{{ route('admin.system.webhooks.retry', $event->id) }}" method="POST" class="d-inline"
                                data-confirm data-confirm-title="Retry Webhook"
                                data-confirm-text="Retry this webhook event?">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light rounded-end" title="Retry">
                                    <i class="fas fa-redo text-warning"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <p class="text-muted">No webhook events found.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
@endsection
