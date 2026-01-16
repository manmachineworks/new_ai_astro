@extends('layouts.app')

@section('content')
    <div class="container py-4" style="height: calc(100vh - 80px);">
        <div class="row justify-content-center h-100">
            <div class="col-md-8 h-100">
                <div class="glass-card h-100 d-flex flex-column p-0 overflow-hidden">
                    <!-- Header -->
                    <div class="p-3 border-bottom border-white-10 d-flex align-items-center">
                        <div class="position-relative me-3">
                            <img src="https://ui-avatars.com/api/?name=AI+Astro&background=9D50BB&color=fff"
                                class="rounded-circle" width="45" height="45">
                            <span
                                class="position-absolute bottom-0 end-0 p-1 bg-success border border-dark rounded-circle"></span>
                        </div>
                        <div>
                            <h6 class="mb-0">Vedic AI Assistant</h6>
                            <small class="text-gold">Online • ₹5.00/msg</small>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="flex-grow-1 p-4 overflow-auto" id="chatContainer">
                        <!-- Welcome Message -->
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0 me-2">
                                <img src="https://ui-avatars.com/api/?name=AI+Astro&background=9D50BB&color=fff"
                                    class="rounded-circle" width="35" height="35">
                            </div>
                            <div class="p-3 rounded-3 bg-white-10 text-white" style="max-width: 75%;">
                                <p class="mb-0 small">Namaste! I am your AI Vedic Assistant. Ask me anything about your
                                    horoscope or kundli.</p>
                            </div>
                        </div>

                        <!-- Messages will be appended here -->
                    </div>

                    <!-- Input Area -->
                    <div class="p-3 border-top border-white-10">
                        <form id="aiChatForm" onsubmit="event.preventDefault(); sendAiMessage();">
                            <div class="input-group">
                                <input type="text" id="userMessage" class="form-control form-control-cosmic border-0"
                                    placeholder="Type your question..." required>
                                <button type="submit" class="btn btn-cosmic rounded-circle ms-2"
                                    style="width: 45px; height: 45px;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <small class="text-muted mt-2 d-block ms-1">Wallet Balance: <span
                                    id="walletBal">Loading...</span></small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chatContainer');

        function appendMessage(role, content) {
            const isUser = role === 'user';
            const align = isUser ? 'justify-content-end' : 'justify-content-start';
            const bg = isUser ? 'bg-primary' : 'bg-white-10 text-white';
            const avatar = isUser ? '' : `<div class="flex-shrink-0 me-2"><img src="https://ui-avatars.com/api/?name=AI+Astro&background=9D50BB&color=fff" class="rounded-circle" width="35" height="35"></div>`;

            const html = `
                <div class="d-flex mb-3 ${align}">
                    ${!isUser ? avatar : ''}
                    <div class="p-3 rounded-3 ${bg}" style="max-width: 75%; ${isUser ? 'background-color: var(--color-primary); color: #000;' : ''}">
                        <p class="mb-0 small">${content}</p>
                    </div>
                </div>
            `;

            chatContainer.insertAdjacentHTML('beforeend', html);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        async function sendAiMessage() {
            const input = document.getElementById('userMessage');
            const message = input.value;
            if (!message) return;

            // Optimistic UI
            appendMessage('user', message);
            input.value = '';
            input.disabled = true;

            try {
                // Since we are Web, we need to handle Auth. 
                // If using Sanctum API, we need a token OR `EnsureFrontendRequestsAreStateful`.
                // For simplicity in this demo, we'll assume the same session-based controller or a helper.
                // But wait, the API is `auth:sanctum`.
                // Use standard fetch. If it fails due to auth, we might need to adjust route to allow 'web' middleware for this demo.

                const response = await fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                        // Missing Authorization Bearer if relying purely on Sanctum.
                        // However, Laravel Sanctum SPA mode allows cookie based auth if configured.
                    },
                    body: JSON.stringify({ message: message })
                });

                if (response.status === 401 || response.status === 419) {
                    appendMessage('assistant', "Error: detailed auth setup required for API from Web. Please check console.");
                    return;
                }

                const data = await response.json();

                if (response.ok) {
                    appendMessage('assistant', data.ai_message.content);
                    // Update balance if returned
                    if (data.remaining_balance) {
                        document.getElementById('walletBal').innerText = '₹ ' + parseFloat(data.remaining_balance).toFixed(2);
                    }
                } else {
                    appendMessage('assistant', "Error: " + (data.message || 'Briefly unavailable'));
                }

            } catch (e) {
                console.error(e);
                appendMessage('assistant', "Connection Error");
            } finally {
                input.disabled = false;
                input.focus();
            }
        }
    </script>

    <style>
        .bg-white-10 {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
@endsection