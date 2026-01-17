@extends('admin.layouts.app')

@section('title', 'Revenue Report')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Revenue Summary</h2>
                <div class="text-muted small">Calls, Human Chat, and AI revenue totals</div>
            </div>
        </div>

        <x-admin.report-tabs />

        <x-admin.report-filters
            :action="route('admin.reports.revenue')"
            :range="$range"
            :exportRoute="route('admin.reports.export')"
            :exportParams="['report' => 'revenue-summary']" />

        @php
            $grossTotal = ($totals->call_gross ?? 0) + ($totals->chat_gross ?? 0) + ($totals->ai_gross ?? 0);
            $commissionTotal = ($totals->call_commission ?? 0) + ($totals->chat_commission ?? 0) + ($totals->ai_commission ?? 0);
            $earningsTotal = $grossTotal - $commissionTotal;
        @endphp

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <x-admin.kpi-card title="Total Gross" value="INR {{ number_format($grossTotal, 2) }}" icon="fas fa-wallet" variant="primary" />
            </div>
            <div class="col-md-4">
                <x-admin.kpi-card title="Total Commission" value="INR {{ number_format($commissionTotal, 2) }}" icon="fas fa-percent" variant="success" />
            </div>
            <div class="col-md-4">
                <x-admin.kpi-card title="Astrologer Earnings" value="INR {{ number_format($earningsTotal, 2) }}" icon="fas fa-hand-holding-usd" variant="info" />
            </div>
        </div>

        <x-admin.table :columns="['Date (IST)', 'Calls Gross', 'Chat Gross', 'AI Gross', 'Commission', 'Earnings', 'Items']" :rows="$metrics">
            @forelse($metrics as $metric)
                @php
                    $day = $metric->date_ist->format('Y-m-d');
                    $dayParams = ['preset' => 'custom', 'start_date' => $day, 'end_date' => $day];
                    $dayGross = $metric->call_gross + $metric->chat_gross + $metric->ai_gross;
                    $dayCommission = $metric->call_commission + $metric->chat_commission + $metric->ai_commission;
                @endphp
                <tr>
                    <td class="ps-4 fw-bold">{{ $metric->date_ist->format('d M Y') }}</td>
                    <td>INR {{ number_format($metric->call_gross, 2) }}</td>
                    <td>INR {{ number_format($metric->chat_gross, 2) }}</td>
                    <td>INR {{ number_format($metric->ai_gross, 2) }}</td>
                    <td class="text-success fw-bold">INR {{ number_format($dayCommission, 2) }}</td>
                    <td class="fw-bold">INR {{ number_format($dayGross - $dayCommission, 2) }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn-light" href="{{ route('admin.reports.revenue.items', array_merge($dayParams, ['type' => 'call'])) }}">Calls</a>
                            <a class="btn btn-light" href="{{ route('admin.reports.revenue.items', array_merge($dayParams, ['type' => 'chat'])) }}">Chats</a>
                            <a class="btn btn-light" href="{{ route('admin.reports.revenue.items', array_merge($dayParams, ['type' => 'ai'])) }}">AI</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No revenue data for this range.</td>
                </tr>
            @endforelse
        </x-admin.table>

    </div>
@endsection



