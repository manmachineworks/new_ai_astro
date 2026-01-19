@extends('layouts.user')

@section('header')
    <x-ui.page-header title="My Membership" description="Manage your active plan and view subscription history."
        :breadcrumbs="[['label' => 'Membership']]" />
@endsection

@section('content')
    <div class="row g-4">
        {{-- Current Plan --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-md-5 text-center">
                    <h5 class="fw-bold text-dark mb-4">Current Plan</h5>

                    @if($membership)
                        <div class="mb-4">
                            <span class="badge bg-warning text-dark fs-5 px-3 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-patch-check-fill me-2"></i>{{ $membership->plan->name ?? 'Premium Plan' }}
                            </span>
                        </div>

                        <div class="bg-light p-3 rounded-4 mb-4">
                            <h3 class="fw-bold text-dark mb-1">Active</h3>
                            <p class="text-muted small mb-0">Expires on: <span
                                    class="fw-semibold">{{ $membership->ends_at_utc ? $membership->ends_at_utc->format('M d, Y') : 'N/A' }}</span>
                            </p>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-3 px-1">Plan Usage & Benefits</h6>
                            <div class="list-group list-group-flush rounded-4 overflow-hidden border">
                                @forelse($membership->usage ?? [] as $usage)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span
                                            class="text-muted small fw-medium">{{ ucwords(str_replace('_', ' ', $usage->benefit_key)) }}</span>
                                        <span class="badge bg-primary rounded-pill">{{ $usage->used_count }}</span>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center py-4">
                                        <p class="text-muted small mb-0">No usage recorded yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="py-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-shield-lock fs-1 text-muted"></i>
                            </div>
                            <h5 class="fw-bold text-dark">No Active Membership</h5>
                            <p class="text-muted small mb-4">Upgrade to a premium plan to unlock exclusive benefits and priority
                                consultations.</p>
                            <a href="{{ route('memberships.index') }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                                Browse Plans
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Subscription History --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Subscription History</h5>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 small text-uppercase text-muted">Plan</th>
                                    <th class="small text-uppercase text-muted">Status</th>
                                    <th class="small text-uppercase text-muted">Start Date</th>
                                    <th class="small text-uppercase text-muted">End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="fw-bold text-dark">{{ $item->plan->name ?? 'Unknown Plan' }}</span>
                                        </td>
                                        <td>
                                            <x-ui.badge :color="$item->status == 'active' ? 'success' : 'secondary'"
                                                :label="ucfirst($item->status)" />
                                        </td>
                                        <td class="text-muted small">
                                            {{ $item->starts_at_utc ? $item->starts_at_utc->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $item->ends_at_utc ? $item->ends_at_utc->format('M d, Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <x-ui.empty-state title="No history found"
                                                description="You haven't purchased any membership plans yet." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection