@extends('layouts.app')

@section('title', 'AI Astrology Chat')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 rounded-4 bg-primary text-white p-4 h-100">
                    <i class="fas fa-robot fs-1 mb-3 opacity-75"></i>
                    <h3 class="fw-bold">AI Astrologer</h3>
                    <p class="opacity-75 small">Get instant answers to your questions about love, career, health, and more
                        based on your planetary positions.</p>
                    <form action="{{ route('user.ai_chat.start') }}" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="fas fa-plus me-2"></i> START NEW CHAT
                        </button>
                        <div class="mt-3 small opacity-75 text-center">
                            Min. ₹{{ \App\Models\PricingSetting::get('ai_chat_min_wallet_to_start', 50.00) }} wallet balance
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold">Recent Consultations</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Mode</th>
                                        <th>Messages</th>
                                        <th>Cost</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sessions as $session)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold small">{{ $session->created_at->format('d M Y, h:i A') }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light text-dark border">
                                                    {{ str_replace('_', ' ', ucfirst($session->pricing_mode)) }}
                                                </span>
                                            </td>
                                            <td>{{ $session->total_messages }}</td>
                                            <td>₹{{ number_format($session->total_charged, 2) }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('user.ai_chat.show', $session->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Open History
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="opacity-50 mb-3"><i class="fas fa-comment-slash fs-1"></i></div>
                                                <span class="text-muted">You haven't started any AI chats yet.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($sessions->hasPages())
                        <div class="card-footer bg-white py-3">
                            {{ $sessions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection