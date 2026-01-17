@extends('admin.layouts.app')

@section('title', $campaign->name)
@section('page_title', 'Campaign: ' . $campaign->code)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Campaign Details</h5>
                    <div>
                        <a href="{{ route('admin.promos.edit', $campaign) }}"
                            class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.promos.toggle', $campaign) }}" class="d-inline">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-{{ $campaign->status == 'active' ? 'warning' : 'success' }}">
                                {{ $campaign->status == 'active' ? 'Pause' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th width="200">Name:</th>
                            <td>{{ $campaign->name }}</td>
                        </tr>
                        <tr>
                            <th>Code:</th>
                            <td><code class="fs-5">{{ $campaign->code }}</code></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><span class="badge bg-secondary">{{ ucfirst($campaign->type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Value:</th>
                            <td>
                                {{ $campaign->discount_type == 'percent' ? $campaign->discount_value . '%' : '₹' . $campaign->discount_value }}
                                @if($campaign->max_discount_amount)
                                    <span class="text-muted">(max ₹{{ $campaign->max_discount_amount }})</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Min Amount:</th>
                            <td>₹{{ $campaign->min_transaction_amount }}</td>
                        </tr>
                        <tr>
                            <th>Applies To:</th>
                            <td>
                                @foreach($campaign->applies_to as $service)
                                    <span class="badge bg-light text-dark">{{ ucfirst($service) }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Valid Period:</th>
                            <td>
                                {{ $campaign->valid_from ? $campaign->valid_from->format('d M Y') : 'Anytime' }}
                                →
                                {{ $campaign->valid_until ? $campaign->valid_until->format('d M Y') : 'No expiry' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Usage Limits:</th>
                            <td>
                                {{ $campaign->usage_limit_per_user }} per user /
                                {{ $campaign->max_total_usage ?? '∞' }} total
                            </td>
                        </tr>
                        <tr>
                            <th>First Time Only:</th>
                            <td>{{ $campaign->first_time_only ? 'Yes' : 'No' }}</td>
                        </tr>
                        @if($campaign->internal_notes)
                            <tr>
                                <th>Notes:</th>
                                <td>{{ $campaign->internal_notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Redemptions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Service</th>
                                    <th>Discount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaign->redemptions()->with('user')->latest()->take(10)->get() as $redemption)
                                    <tr>
                                        <td>{{ $redemption->user->name }}</td>
                                        <td>{{ class_basename($redemption->reference_type) }}</td>
                                        <td>₹{{ $redemption->discount_amount }}</td>
                                        <td>{{ $redemption->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No redemptions yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Redemptions</h6>
                    <h2>{{ $campaign->redemptions()->count() }}</h2>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Discounted</h6>
                    <h2>₹{{ number_format($campaign->redemptions()->sum('discount_amount'), 2) }}</h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Unique Users</h6>
                    <h2>{{ $campaign->redemptions()->distinct('user_id')->count('user_id') }}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush