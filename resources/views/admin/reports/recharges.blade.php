@extends('admin.layouts.app')

@section('title', 'Wallet Recharges Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Wallet Recharges</h2>
            <div>
                <a href="{{ route('admin.reports.recharges', array_merge(request()->all(), ['export' => 1])) }}"
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
                            <th class="ps-4">Order ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date (IST)</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="small text-muted">{{ $order->merchant_transaction_id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <div class="small text-muted">{{ $order->user->phone }}</div>
                                </td>
                                <td class="fw-bold">₹{{ $order->amount }}</td>
                                <td>
                                    @if($order->status === 'PAID')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">PAID</span>
                                    @elseif($order->status === 'FAILED')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">FAILED</span>
                                    @else
                                        <span
                                            class="badge bg-warning-subtle text-warning rounded-pill px-3">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $order->updated_at->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.payments.show', $order->id) }}"
                                        class="btn btn-sm btn-light rounded-pill">Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection