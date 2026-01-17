@extends('admin.layouts.app')

@section('title', 'Refunds & Disputes')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Refunds & Disputes</h2>
                <div class="text-muted small">Completed and in-flight refund ledger</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters
            :action="route('admin.reports.refunds')"
            :range="$range"
            :exportRoute="route('admin.reports.refunds')"
            :exportParams="['export' => 1]">
            <select name="status" class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                <option value="">All Statuses</option>
                <option value="initiated" {{ request('status') === 'initiated' ? 'selected' : '' }}>Initiated</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </x-admin.report-filters>

        <x-admin.table :columns="['Date (IST)', 'Refund ID', 'User', 'Amount', 'Status', 'Reason', 'Reference']" :rows="$refunds">
            @forelse($refunds as $refund)
                @php
                    $ts = $refund->updated_at?->setTimezone('Asia/Kolkata');
                @endphp
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">{{ $ts?->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $ts?->format('H:i') }}</div>
                    </td>
                    <td class="font-monospace small">{{ $refund->id }}</td>
                    <td>{{ $refund->user_name ?? '-' }}</td>
                    <td class="fw-bold">INR {{ number_format($refund->amount ?? 0, 2) }}</td>
                    <td>{{ ucfirst($refund->status) }}</td>
                    <td class="text-muted">{{ $refund->reason }}</td>
                    <td class="text-end pe-4 font-monospace small">{{ $refund->reference_type }} #{{ $refund->reference_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No refunds in this range.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
@endsection
