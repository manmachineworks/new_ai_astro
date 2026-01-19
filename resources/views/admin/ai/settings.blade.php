@extends('admin.layouts.app')

@section('title', 'AI Astrologer Settings')
@section('page_title', 'AI Settings')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Configuration</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.ai.settings.update') }}" method="POST">
                        @csrf

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="ai_chat_enabled" name="ai_chat_enabled"
                                value="1" {{ $settings['ai_chat_enabled'] ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="ai_chat_enabled">Enable AI Astrologer
                                Feature</label>
                            <div class="form-text">When disabled, the AI chat option will be hidden from all users.</div>
                        </div>

                        <h6 class="text-uppercase text-muted small border-bottom pb-2 mb-3">Pricing Strategy</h6>

                        <div class="mb-3">
                            <label class="form-label">Pricing Mode</label>
                            <select name="ai_chat_pricing_mode" class="form-select">
                                <option value="per_message" {{ $settings['ai_chat_pricing_mode'] == 'per_message' ? 'selected' : '' }}>Per Message</option>
                                <option value="per_session" {{ $settings['ai_chat_pricing_mode'] == 'per_session' ? 'selected' : '' }}>Per Session (Flat Fee)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price Per Message</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="ai_chat_price_per_message" class="form-control"
                                        value="{{ $settings['ai_chat_price_per_message'] }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price Per Session</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="ai_chat_price_per_session" class="form-control"
                                        value="{{ $settings['ai_chat_price_per_session'] }}">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small border-bottom pb-2 mb-3 mt-2">Limits & Usage</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min. Wallet Balance to Start</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="ai_chat_min_wallet_to_start" class="form-control"
                                        value="{{ $settings['ai_chat_min_wallet_to_start'] }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Messages Per Day (User)</label>
                                <input type="number" name="ai_chat_max_messages_per_day" class="form-control"
                                    value="{{ $settings['ai_chat_max_messages_per_day'] }}">
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small border-bottom pb-2 mb-3 mt-2">Provider Settings</h6>

                        <div class="mb-3">
                            <label class="form-label">AstrologyAPI Base URL</label>
                            <input type="text" name="astrology_api_base_url" class="form-control"
                                value="{{ $settings['astrology_api_base_url'] ?? config('astrologyapi.base_url') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">AstrologyAPI User ID</label>
                            <input type="text" name="astrology_api_user_id" class="form-control"
                                value="{{ $settings['astrology_api_user_id'] ?? config('astrologyapi.user_id') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">AstrologyAPI Key</label>
                            <input type="password" name="astrology_api_key" class="form-control"
                                placeholder="Leave blank to keep current">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Request Timeout (seconds)</label>
                                <input type="number" name="astrology_api_timeout" class="form-control"
                                    value="{{ $settings['astrology_api_timeout'] ?? config('astrologyapi.timeout') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rate Limit / Min</label>
                                <input type="number" name="ai_chat_rate_limit_per_min" class="form-control"
                                    value="{{ $settings['ai_chat_rate_limit_per_min'] ?? config('astrologyapi.limits.rate_limit_per_min') }}">
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted small border-bottom pb-2 mb-3 mt-2">Legal & Content</h6>

                        <div class="mb-3">
                            <label class="form-label">Disclaimer Text</label>
                            <textarea name="ai_chat_disclaimer_text" class="form-control"
                                rows="3">{{ $settings['ai_chat_disclaimer_text'] }}</textarea>
                            <div class="form-text">This text is displayed at the start of every AI session.</div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>About AI Astrologer</h6>
                    <p class="small text-muted mb-0">
                        The AI Astrologer uses the configured LLM API to provide automated astrology readings.
                        Ensure your API keys are correctly set in the <code>.env</code> file.
                    </p>
                    <hr>
                    <h6 class="fw-bold">Provider Status</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="d-flex justify-content-between mb-1">
                            <span>AstrologyAPI:</span>
                            <span class="badge bg-{{ !empty($settings['astrology_api_user_id']) ? 'success' : 'secondary' }}">
                                {{ !empty($settings['astrology_api_user_id']) ? 'Configured' : 'Missing' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Endpoint:</span>
                            <span class="text-muted">{{ $settings['astrology_api_base_url'] ?? config('astrologyapi.base_url') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
