@extends('layouts.admin')

@section('title', 'AI Chat Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">AI Astrology Chat Configuration</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.ai.settings.update') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ai_chat_enabled" id="ai_chat_enabled"
                                    value="1" {{ $settings['ai_chat_enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="ai_chat_enabled">Enable AI Chat</label>
                                <div class="form-text">Toggle the visibility of AI Chat for all users.</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pricing Mode</label>
                                <select name="ai_chat_pricing_mode" class="form-select">
                                    <option value="per_message" {{ $settings['ai_chat_pricing_mode'] == 'per_message' ? 'selected' : '' }}>Per Message</option>
                                    <option value="per_session" {{ $settings['ai_chat_pricing_mode'] == 'per_session' ? 'selected' : '' }}>Per Session</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Min Wallet to Start (₹)</label>
                                <input type="number" step="0.01" name="ai_chat_min_wallet_to_start" class="form-control"
                                    value="{{ $settings['ai_chat_min_wallet_to_start'] }}" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price Per Message (₹)</label>
                                <input type="number" step="0.01" name="ai_chat_price_per_message" class="form-control"
                                    value="{{ $settings['ai_chat_price_per_message'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Price Per Session (₹)</label>
                                <input type="number" step="0.01" name="ai_chat_price_per_session" class="form-control"
                                    value="{{ $settings['ai_chat_price_per_session'] }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Daily Message Limit per User</label>
                            <input type="number" name="ai_chat_max_messages_per_day" class="form-control"
                                value="{{ $settings['ai_chat_max_messages_per_day'] }}" required>
                            <div class="form-text">Maximum messages a user can send to AI in 24 hours.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">AI Disclaimer Text</label>
                            <textarea name="ai_chat_disclaimer_text" class="form-control" rows="4"
                                required>{{ $settings['ai_chat_disclaimer_text'] }}</textarea>
                            <div class="form-text">This will be shown to users when they start an AI chat.</div>
                        </div>

                        <div class="d-grid shadow-sm">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">Save AI Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection