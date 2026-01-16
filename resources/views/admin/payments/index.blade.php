@extends('admin.layouts.app')

@section('title', 'Payment Orders')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Orders</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Txn ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-light text-primary me-2">
                                        {{ substr($order->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $order->user->name }}</div>
                                        <div class="small text-muted">{{ $order->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-primary">{{ $order->merchant_transaction_id }}</code></td>
                            <td class="fw-bold">₹{{ number_format($order->amount, 2) }}</td>
                            <td>
                                @if($order->status === 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($order->status === 'initiated')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.payments.show', $order->id) }}"
                                    class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $orders->links() }}
        </div>
    </div>
@endsection