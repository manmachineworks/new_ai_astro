@extends('layouts.user')

@section('header')
    <x-ui.page-header title="AI Assistant" description="Get instant astrological answers powered by AI." />
@endsection

@section('content')
    <div class="card border-0 shadow-sm mx-auto overflow-hidden"
        style="max-width: 900px; height: calc(100vh - 250px); min-height: 600px;">
        {{-- Banner --}}
        <div class="p-3 border-bottom bg-primary bg-opacity-10">
            <x-user.ai.price-banner :price="15" />
        </div>

        {{-- Chat Area --}}
        <div class="card-body overflow-y-auto p-4 d-flex flex-column gap-4 bg-light" id="ai-chat-messages">
            {{-- Welcome Message --}}
            <div class="d-flex justify-content-start">
                <div class="d-flex align-items-start" style="max-width: 80%;">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width: 35px; height: 35px; font-size: 0.75rem;">
                            AI
                        </div>
                    </div>
                    <div class="bg-white border rounded-3 rounded-top-0 p-3 text-sm text-dark shadow-sm">
                        <p class="mb-2">Namaste! I am your AI Astrologer. I have analyzed your birth chart.</p>
                        <p class="mb-0">You can ask me anything about your career, relationships, health, or daily
                            horoscope.</p>
                    </div>
                </div>
            </div>

            {{-- Example User Message --}}
            <div class="d-flex justify-content-end">
                <div class="d-flex flex-column align-items-end" style="max-width: 80%;">
                    <div class="bg-primary text-white rounded-3 rounded-bottom-end-0 p-3 text-sm shadow-sm">
                        <p class="mb-0">What does my career look like in 2024?</p>
                    </div>
                </div>
            </div>

            {{-- Example AI Response --}}
            <div class="d-flex justify-content-start">
                <div class="d-flex align-items-start" style="max-width: 80%;">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width: 35px; height: 35px; font-size: 0.75rem;">
                            AI
                        </div>
                    </div>
                    <div class="bg-white border rounded-3 rounded-top-0 p-3 text-sm text-dark shadow-sm">
                        <p class="mb-2">Based on your chart, 2024 brings significant growth opportunities in your career
                            sector (10th house). Jupiter's transit suggests a promotion or new role around mid-year.</p>
                        <p class="mb-0">However, Saturn indicates you need to work diligently and avoid shortcuts. April
                            might be challenging but rewarding.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="card-footer bg-white border-top p-4">
            <div class="mb-3">
                <x-user.ai.prompt-chips />
            </div>

            <form action="{{ route('user.ai.send') }}" method="POST" class="position-relative">
                @csrf
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <input type="text" name="message" class="form-control border-0 bg-light py-3 px-4"
                        placeholder="Ask a question..." required>
                    <button type="submit" class="btn btn-primary px-4 border-0">
                        <i class="bi bi-send-fill" style="transform: rotate(45deg);"></i>
                    </button>
                </div>
            </form>
            <p class="text-muted text-center small mt-3 mb-0" style="font-size: 0.7rem;">
                AI can make mistakes. Please verify important decisions.
            </p>
        </div>
    </div>
@endsection