@extends('admin.layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Commission Settings')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold m-0">Commission Settings</h2>
                <div class="text-muted small">Review platform commission tiers and payout rules.</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <p class="text-muted">Values are loaded from <code>pricing_settings</code> so changes will be reflected wherever the service is used.</p>

                @if($settings->isEmpty())
                    <div class="alert alert-warning text-muted">
                        No commission config found. Run the pricing seeders or add entries via the admin settings.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                                <tr>
                                    <th>Key</th>
                                    <th>Description</th>
                                    <th>Value</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ Str::of($setting->key)->replace('_', ' ')->title() }}</td>
                                        <td class="text-muted small">
                                            @php
                                                $description = match ($setting->key) {
                                                    'platform_commission' => 'Percentage taken from each transaction.',
                                                    'call_commission' => 'Per-minute commission split for calls.',
                                                    'chat_commission' => 'Per-message/session commission for chat revenue.',
                                                    default => 'Custom pricing setting.'
                                                };
                                            @endphp
                                            {{ $description }}
                                        </td>
                                        <td>
                                            <pre class="small mb-0 text-muted">{{ json_encode($setting->value_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </td>
                                        <td class="text-muted small">{{ $setting->updated_at?->format('d M Y H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
