@props([
    'action',
    'range',
    'exportRoute' => null,
    'exportParams' => [],
    'chips' => []
])

@php
    $exportQuery = array_merge(request()->except(['page', 'export']), $exportParams);
@endphp

<form action="{{ $action }}" method="GET" class="d-flex flex-wrap gap-2 align-items-center bg-white p-2 rounded-4 shadow-sm">
    <select name="preset" class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
        <option value="today" {{ $range['preset'] === 'today' ? 'selected' : '' }}>Today</option>
        <option value="yesterday" {{ $range['preset'] === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
        <option value="last_7_days" {{ $range['preset'] === 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
        <option value="last_30_days" {{ $range['preset'] === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
        <option value="this_month" {{ $range['preset'] === 'this_month' ? 'selected' : '' }}>This Month</option>
        <option value="custom" {{ $range['preset'] === 'custom' ? 'selected' : '' }}>Custom</option>
    </select>
    <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
        value="{{ $range['start_ist']->toDateString() }}">
    <span class="text-muted small">to</span>
    <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
        value="{{ $range['end_ist']->toDateString() }}">

    {{ $slot }}

    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Apply</button>
    <a href="{{ $action }}" class="btn btn-light btn-sm rounded-pill px-3">Reset</a>

    @if($exportRoute)
        <a href="{{ $exportRoute }}?{{ http_build_query($exportQuery) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </a>
    @endif
</form>

<div class="d-flex flex-wrap gap-2 mt-2">
    <span class="badge bg-light text-dark border">IST: {{ $range['start_ist']->format('d M Y') }} to {{ $range['end_ist']->format('d M Y') }}</span>
    @if($range['preset'] !== 'custom')
        <span class="badge bg-light text-dark border">Preset: {{ str_replace('_', ' ', ucfirst($range['preset'])) }}</span>
    @endif
    @foreach($chips as $chip)
        <span class="badge bg-secondary-subtle text-secondary border">{{ $chip }}</span>
    @endforeach
</div>
