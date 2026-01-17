@extends('admin.layouts.app')

@section('title', 'Chats Analytics')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Chats Analytics</h2>
                <div class="text-muted small">Human chat sessions</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters
            :action="route('admin.reports.chats')"
            :range="$range"
            :exportRoute="route('admin.reports.chats')"
            :exportParams="['export' => 1]" />

        <x-admin.table :columns="['ID', 'Date (IST)', 'User', 'Astrologer', 'Messages', 'Gross', 'Commission', 'Status', 'Actions']" :rows="$sessions">
            @forelse($sessions as $session)
                @php
                    $ts = $session->updated_at?->setTimezone('Asia/Kolkata');
                @endphp
                <tr>
                    <td class="ps-4 font-monospace small">#{{ $session->id }}</td>
                    <td>
                        <div class="fw-bold">{{ $ts?->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $ts?->format('H:i') }}</div>
                    </td>
                    <td>{{ $session->user?->name ?? '-' }}</td>
                    <td>{{ $session->astrologerProfile?->user?->name ?? '-' }}</td>
                    <td>{{ $session->total_messages_user ?? 0 }}</td>
                    <td>INR {{ number_format($session->total_charged ?? 0, 2) }}</td>
                    <td>INR {{ number_format($session->commission_amount_total ?? 0, 2) }}</td>
                    <td>{{ ucfirst($session->status) }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.chats.show', $session->id) }}" class="btn btn-sm btn-light rounded-pill">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No chat sessions in this range.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
@endsection



