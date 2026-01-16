@extends('admin.layouts.app')

@section('title', 'Pricing Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Global Pricing Configuration</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.pricing.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h6 class="text-primary mt-3 border-bottom pb-2">Wallet Gates</h6>
                        <div class="mb-3">
                            <label class="form-label">Min Wallet to Start Call (₹)</label>
                            <input type="number" name="min_wallet_to_start_call" class="form-control"
                                value="{{ $settings['min_wallet_to_start_call'] ?? 50 }}" step="0.01">
                            <div class="form-text">User must have at least this amount to initiate a call.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Min Wallet to Start Chat (₹)</label>
                            <input type="number" name="min_wallet_to_start_chat" class="form-control"
                                value="{{ $settings['min_wallet_to_start_chat'] ?? 30 }}" step="0.01">
                        </div>

                        <h6 class="text-primary mt-4 border-bottom pb-2">AI Services</h6>
                        <div class="mb-3">
                            <label class="form-label">AI Chat Price (Per Message) (₹)</label>
                            <input type="number" name="ai_chat_price_per_message" class="form-control"
                                value="{{ $settings['ai_chat_price_per_message'] ?? 5 }}" step="0.01">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection