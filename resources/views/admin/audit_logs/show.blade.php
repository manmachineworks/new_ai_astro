@extends('admin.layouts.app')

@section('title', 'Audit Log Detail #' . $log->id)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold m-0 text-dark">Log Detail #{{ $log->id }}</h2>
                    <div class="text-muted">{{ $log->created_at->format('F d, Y H:i:s') }}</div>
                </div>
                <a href="{{ route('admin.system.audit_logs.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Logs
                </a>
            </div>

            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Action Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="small text-uppercase text-muted fw-bold mb-1">Performed By</div>
                            @if($log->causer)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle-sm bg-primary-subtle text-primary me-2 rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:40px;height:40px;">
                                        {{ substr($log->causer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $log->causer->name }}</div>
                                        <div class="small text-muted">{{ $log->causer->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted fst-italic">System or Deleted User</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="small text-uppercase text-muted fw-bold mb-1">Action Type</div>
                            <span class="badge bg-primary fs-6">{{ $log->action }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-uppercase text-muted fw-bold mb-1">Target</div>
                            @if($log->target_type)
                                <div class="fw-bold">{{ class_basename($log->target_type) }}</div>
                                <div class="font-monospace text-muted">ID: {{ $log->target_id }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Metadata & Payload</h5>
                </div>
                <div class="card-body p-4 bg-light font-monospace">
                    <pre class="mb-0">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>

            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Technical Context</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-uppercase text-muted fw-bold mb-1">IP Address</div>
                            <div>{{ $log->ip_address }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-uppercase text-muted fw-bold mb-1">User Agent</div>
                            <div class="small text-muted">{{ $log->user_agent }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection