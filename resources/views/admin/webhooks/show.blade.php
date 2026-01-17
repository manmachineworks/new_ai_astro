@extends('admin.layouts.app')

@section('title', 'Webhook Event Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('admin.system.webhooks.index') }}"
                    class="text-decoration-none text-muted small mb-2 d-block">
                    <i class="fas fa-arrow-left me-1"></i> Back to Webhooks
                </a>
                <h2 class="fw-bold m-0">Event: {{ $event->event_type }}</h2>
            </div>
            <div class="d-flex gap-2">
                @if($event->processing_status === 'failed')
                    <form action="{{ route('admin.system.webhooks.retry', $event->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning rounded-pill px-4 text-white fw-bold">
                            <i class="fas fa-redo me-2"></i>Retry Event
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold m-0">Metadata</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush">
                            <li
                                class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small text-uppercase fw-bold">Provider</span>
                                <span class="fw-bold text-capitalize">{{ $event->provider }}</span>
                            </li>
                            <li
                                class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small text-uppercase fw-bold">External ID</span>
                                <span class="font-monospace small">{{ $event->external_id }}</span>
                            </li>
                            <li
                                class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small text-uppercase fw-bold">Signature Valid</span>
                                @if($event->signature_valid)
                                    <span class="badge bg-success-subtle text-success"><i
                                            class="fas fa-check-circle me-1"></i>Valid</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger"><i
                                            class="fas fa-times-circle me-1"></i>Invalid</span>
                                @endif
                            </li>
                            <li
                                class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small text-uppercase fw-bold">Received At</span>
                                <span>{{ $event->created_at->format('d M Y H:i:s') }}</span>
                            </li>
                            <li
                                class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small text-uppercase fw-bold">Processed At</span>
                                <span>{{ $event->processed_at ? $event->processed_at->format('d M Y H:i:s') : 'N/A' }}</span>
                            </li>
                        </ul>

                        <div class="mt-4 p-3 bg-light rounded-3">
                            <div class="small text-uppercase text-muted fw-bold mb-2">Processing Status</div>
                            @if($event->processing_status == 'completed')
                                <div class="text-success fw-bold"><i class="fas fa-check-circle me-2"></i>Success</div>
                            @elseif($event->processing_status == 'failed')
                                <div class="text-danger fw-bold"><i class="fas fa-times-circle me-2"></i>Failed</div>
                                <div class="mt-2 text-danger small bg-white p-2 rounded border border-danger">
                                    {{ $event->error_message ?? 'Unknown Error' }}
                                </div>
                            @else
                                <div class="text-warning fw-bold"><i
                                        class="fas fa-clock me-2"></i>{{ ucfirst($event->processing_status) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold m-0">Payload</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="nav nav-tabs nav-fill mb-3" id="payloadTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill" id="body-tab" data-bs-toggle="tab"
                                    data-bs-target="#body" type="button" role="tab">Request Body</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill" id="headers-tab" data-bs-toggle="tab"
                                    data-bs-target="#headers" type="button" role="tab">Headers</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="payloadTabContent">
                            <div class="tab-pane fade show active" id="body" role="tabpanel">
                                <div class="bg-dark text-light p-3 rounded-3" style="max-height: 500px; overflow-y: auto;">
                                    <pre
                                        class="m-0"><code class="language-json">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="headers" role="tabpanel">
                                <div class="bg-dark text-light p-3 rounded-3" style="max-height: 500px; overflow-y: auto;">
                                    <pre
                                        class="m-0"><code class="language-json">{{ json_encode($event->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection