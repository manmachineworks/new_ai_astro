@extends('admin.layouts.app')

@section('title', 'Revenue Breakdown')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Revenue Summary</h2>
            <div>
                <a href="{{ route('admin.reports.revenue', array_merge(request()->all(), ['export' => 1])) }}"
                    class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </a>
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-light rounded-pill px-4 ms-2">Back to
                    Dashboard</a>
            </div>
        </div>

        @php
            $startDate = $range['start']->toDateString();
            $endDate = $range['end']->toDateString();
            $metrics = \App\Models\DailyMetric::whereBetween('date_ist', [$startDate, $endDate])->orderBy('date_ist', 'desc')->paginate(20);
        @endphp

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Date (IST)</th>
                            <th>Calls Gross</th>
                            <th>Chat Gross</th>
                            <th>AI Gross</th>
                            <th>Commission</th>
                            <th class="pe-4 text-end">Marketplace Gross</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metrics as $metric)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $metric->date_ist->format('d M Y') }}</td>
                                <td>₹{{ number_format($metric->call_gross, 2) }}</td>
                                <td>₹{{ number_format($metric->chat_gross, 2) }}</td>
                                <td>₹{{ number_format($metric->ai_gross, 2) }}</td>
                                <td class="text-danger">
                                    ₹{{ number_format($metric->call_commission + $metric->chat_commission + $metric->ai_commission, 2) }}
                                </td>
                                <td class="pe-4 text-end fw-bold">
                                    ₹{{ number_format($metric->call_gross + $metric->chat_gross + $metric->ai_gross, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4">
                {{ $metrics->links() }}
            </div>
        </div>
    </div>
@endsection