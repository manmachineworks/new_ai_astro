@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">My Membership</h2>

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">Current Plan</div>
                    <div class="card-body text-center p-4">
                        @if($membership)
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark fs-5 px-3 py-2">
                                    <i class="fas fa-crown"></i> {{ $membership->plan->name }}
                                </span>
                            </div>
                            <h3 class="mb-2">Active</h3>
                            <p class="text-muted">Expires on: {{ $membership->ends_at_utc->format('M d, Y') }}</p>

                            <hr>
                            <h5 class="text-start">Benefits Used</h5>
                            <ul class="list-group list-group-flush text-start">
                                @foreach($membership->usage as $usage)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ ucwords(str_replace('_', ' ', $usage->benefit_key)) }}</span>
                                        <span class="fw-bold">{{ $usage->used_count }}</span>
                                    </li>
                                @endforeach
                                @if($membership->usage->isEmpty())
                                    <li class="list-group-item text-muted">No usage yet.</li>
                                @endif
                            </ul>

                        @else
                            <p class="lead text-muted">No active membership.</p>
                            <a href="{{ route('memberships.index') }}" class="btn btn-cosmic">Browse Plans</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">History</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $item)
                                        <tr>
                                            <td>{{ $item->plan->name ?? 'Unknown Plan' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $item->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $item->starts_at_utc ? $item->starts_at_utc->format('M d, Y') : '-' }}</td>
                                            <td>{{ $item->ends_at_utc ? $item->ends_at_utc->format('M d, Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @if($history->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No membership history found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection