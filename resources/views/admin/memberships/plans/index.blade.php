@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Membership Plans</h1>
            <a href="{{ route('admin.memberships.plans.create') }}" class="btn btn-primary">Create New Plan</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Benefits</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $plan->name }}</td>
                                    <td>₹{{ $plan->price_amount }}</td>
                                    <td>{{ $plan->duration_days }} Days</td>
                                    <td>
                                        <ul class="small mb-0 list-unstyled">
                                            @if(($plan->benefits_json['call_discount_percent'] ?? 0) > 0)
                                                <li>Call: {{ $plan->benefits_json['call_discount_percent'] }}% Off</li>
                                            @endif
                                            @if(($plan->benefits_json['chat_discount_percent'] ?? 0) > 0)
                                                <li>Chat: {{ $plan->benefits_json['chat_discount_percent'] }}% Off</li>
                                            @endif
                                            @if(($plan->benefits_json['ai_free_messages'] ?? 0) > 0)
                                                <li>AI: {{ $plan->benefits_json['ai_free_messages'] }} Free Msgs</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $plan->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $plan->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.memberships.plans.edit', $plan->id) }}"
                                            class="btn btn-sm btn-info">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection