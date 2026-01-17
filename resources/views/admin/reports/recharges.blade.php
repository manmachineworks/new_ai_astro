@extends('admin.layouts.app')

@section('title', 'Wallet Recharges')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Wallet Recharges</h2>
                <div class="text-muted small">PhonePe payment orders (status-based)</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters
            :action="route('admin.reports.recharges')"
            :range="$range"
            :exportRoute="route('admin.reports.recharges')"
            :exportParams="['export' => 1]">
            <select name="status" class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                <option value="">All Statuses</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </x-admin.report-filters>

        <x-admin.table :columns="['Date (IST)', 'Transaction ID', 'User', 'Amount', 'Status', 'Action']" :rows="$orders">
            @forelse($orders as $order)
                @php
                    $ts = $order->updated_at?->setTimezone('Asia/Kolkata');
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $ts?->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $ts?->format('H:i') }}</div>
                    </td>
                    <td class="font-monospace small">{{ $order->merchant_transaction_id }}</td>
                    <td>
                        @if($order->user)
                            <a href="{{ route('admin.users.show', $order->user_id) }}" class="text-decoration-none fw-bold">{{ $order->user->name }}</a>
                        @else
                            <span class="text-muted">Deleted User</span>
                        @endif
                    </td>
                    <td class="fw-bold text-success">INR {{ number_format($order->amount, 2) }}</td>
                    <td>
                        @php
                            $status = strtolower($order->status ?? '');
                            $badge = $status === 'success' ? 'success' : ($status === 'failed' ? 'danger' : 'warning');
                        @endphp
                        <span class="badge bg-{{ $badge }} rounded-pill px-3">{{ ucfirst($status) }}</span>
                    </td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('admin.payments.show', $order->id) }}" class="btn btn-sm btn-light rounded-pill">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No recharge orders in this range.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
@endsection



