@extends('admin.layouts.app')

@section('title', 'Revenue Line Items')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Revenue Line Items</h2>
                <div class="text-muted small">Ledger-backed charges and settled sessions</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters
            :action="route('admin.reports.revenue.items')"
            :range="$range"
            :exportRoute="route('admin.reports.export')"
            :exportParams="['report' => 'revenue-items', 'type' => $type]"
            :chips="['Type: ' . strtoupper($type)]">
            <select name="type" class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                <option value="call" {{ $type === 'call' ? 'selected' : '' }}>Calls</option>
                <option value="chat" {{ $type === 'chat' ? 'selected' : '' }}>Chats</option>
                <option value="ai" {{ $type === 'ai' ? 'selected' : '' }}>AI</option>
            </select>
        </x-admin.report-filters>

        <x-admin.table :columns="['Date (IST)', 'Type', 'User', 'Astrologer', 'Gross', 'Commission', 'Earnings', 'Reference']" :rows="$items">
            @forelse($items as $item)
                @php
                    $occurredAt = \Carbon\Carbon::parse($item->occurred_at)->setTimezone('Asia/Kolkata');
                @endphp
                <tr>
                    <td class="ps-4 fw-bold">{{ $occurredAt->format('d M Y, H:i') }}</td>
                    <td>{{ strtoupper($item->type) }}</td>
                    <td>{{ $item->user_name ?? '-' }}</td>
                    <td>{{ $item->astrologer_name ?? '-' }}</td>
                    <td>INR {{ number_format((float) $item->gross, 2) }}</td>
                    <td class="text-success">INR {{ number_format((float) $item->commission, 2) }}</td>
                    <td class="fw-bold">INR {{ number_format((float) $item->earnings, 2) }}</td>
                    <td class="pe-4 text-end font-monospace small">{{ $item->reference_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No line items for this range.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
@endsection




