@extends('layouts.app')

@section('title', 'AI Astrology Consultation')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="height: 80vh;">
                    <!-- Header -->
                    <div class="card-header bg-primary py-3 px-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-robot text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">AI Astrologer</h5>
                                <span class="badge bg-light text-primary small rounded-pill">
                                    {{ str_replace('_', ' ', ucfirst($session->pricing_mode)) }}
                                    @if($session->pricing_mode === 'per_message')
                                        (₹{{ $session->price_per_message }}/msg)
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="text-white text-end">
                            <div class="small opacity-75">Wallet Balance</div>
                            <div class="fw-bold" id="wallet-balance">₹{{ number_format(auth()->user()->wallet_balance, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Messages area -->
                    <div class="card-body p-0 d-flex flex-column bg-light">
                        <!-- Disclaimer Banner -->
                        <div class="bg-warning-subtle p-3 small text-center border-bottom border-warning-subtle">
                            <i class="fas fa-info-circle me-1 text-warning"></i>
                            {{ \App\Models\PricingSetting::get('ai_chat_disclaimer_text', 'For guidance only.') }}
                        </div>

                        <div id="chat-messages" class="flex-grow-1 p-4 overflow-auto" style="scroll-behavior: smooth;">
                            @foreach($messages as $message)
                                @if($message->role === 'system')
                                    <div class="text-center my-3">
                                        <span class="badge bg-secondary-subtle text-secondary py-2 px-3 rounded-pill fw-normal">
                                            {{ $message->content }}
                                        </span>
                                    </div>
                                @elseif($message->role === 'user')
                                    <div class="d-flex justify-content-end mb-4">
                                        <div class="bg-primary text-white p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                                            {{ $message->content }}
                                        </div>
                                    </div>
                                @elseif($message->role === 'assistant')
                                    <div class="d-flex justify-content-start mb-4">
                                        <div class="bg-white p-3 rounded-4 shadow-sm border" style="max-width: 80%;">
                                            <div class="mb-2" style="white-space: pre-wrap;">{{ $message->content }}</div>
                                            <div class="text-end border-top pt-1 mt-2">
                                                <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none report-btn"
                                                    data-msg-id="{{ $message->id }}">
                                                    <i class="far fa-flag me-1"></i> Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Input Area -->
                        <div class="bg-white border-top p-3 p-md-4">
                            <form id="ai-chat-form" class="d-flex gap-2">
                                <input type="text" id="ai-message-input" name="message"
                                    class="form-control rounded-pill border-light bg-light px-4"
                                    placeholder="Ask about your destiny..." autocomplete="off">
                                <button type="submit" id="send-btn" class="btn btn-primary rounded-circle"
                                    style="width: 50px; height: 50px;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            <div id="typing-indicator" class="small text-muted mt-2 ps-3 d-none">
                                <i class="fas fa-circle-notch fa-spin me-1"></i> AI is calculating planetary positions...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Report AI Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="report-form">
                    <div class="modal-body">
                        <input type="hidden" name="message_id" id="report-msg-id">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Why are you reporting this?</label>
                            <select name="reason" class="form-select" required>
                                <option value="Incorrect Information">Incorrect Information</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="Harmful Advice">Harmful Advice</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Details (Optional)</label>
                            <textarea name="details" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const chatContainer = document.getElementById('chat-messages');
        const chatForm = document.getElementById('ai-chat-form');
        const msgInput = document.getElementById('ai-message-input');
        const sendBtn = document.getElementById('send-btn');
        const typingIndicator = document.getElementById('typing-indicator');
        const walletBalance = document.getElementById('wallet-balance');
        const session_id = "{{ $session->id }}";

        // Scroll to bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = msgInput.value.trim();
            if (!msg) return;

            // Visual feedback
            const client_id = self.crypto.randomUUID();
            appendUserMessage(msg);
            msgInput.value = '';
            msgInput.disabled = true;
            sendBtn.disabled = true;
            typingIndicator.classList.remove('d-none');
            chatContainer.scrollTop = chatContainer.scrollHeight;

            try {
                const res = await axios.post(`/ai-chat/${session_id}/message`, {
                    message: msg,
                    client_message_id: client_id
                });

                if (res.data.success) {
                    appendAssistantMessage(res.data.message.content, res.data.message.id);
                    walletBalance.innerText = `₹${parseFloat(res.data.balance).toFixed(2)}`;
                }
            } catch (error) {
                let errorMsg = 'Failed to send message.';
                if (error.response) {
                    if (error.response.status === 402 && error.response.data.redirect) {
                        window.location.href = error.response.data.redirect;
                        return;
                    }
                    errorMsg = error.response.data.error || error.response.data.message || errorMsg;
                }
                appendSystemMessage(errorMsg, 'text-danger');
            } finally {
                msgInput.disabled = false;
                sendBtn.disabled = false;
                typingIndicator.classList.add('d-none');
                chatContainer.scrollTop = chatContainer.scrollHeight;
                msgInput.focus();
            }
        });

        function appendUserMessage(text) {
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-end mb-4';
            div.innerHTML = `
                <div class="bg-primary text-white p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                    ${escapeHtml(text)}
                </div>
            `;
            chatContainer.appendChild(div);
        }

        function appendAssistantMessage(text, id) {
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-start mb-4';
            div.innerHTML = `
                <div class="bg-white p-3 rounded-4 shadow-sm border" style="max-width: 80%;">
                    <div class="mb-2" style="white-space: pre-wrap;">${escapeHtml(text)}</div>
                    <div class="text-end border-top pt-1 mt-2">
                        <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none report-btn" data-msg-id="${id}">
                            <i class="far fa-flag me-1"></i> Report
                        </button>
                    </div>
                </div>
            `;
            chatContainer.appendChild(div);

            // Add listener to new report button
            div.querySelector('.report-btn').addEventListener('click', function () {
                showReportModal(id);
            });
        }

        function appendSystemMessage(text, colorClass = 'text-secondary') {
            const div = document.createElement('div');
            div.className = 'text-center my-3';
            div.innerHTML = `
                <span class="badge bg-secondary-subtle ${colorClass} py-2 px-3 rounded-pill fw-normal">
                    ${escapeHtml(text)}
                </span>
            `;
            chatContainer.appendChild(div);
        }

        // Report logic
        document.addEventListener('click', function (e) {
            if (e.target.closest('.report-btn')) {
                const btn = e.target.closest('.report-btn');
                showReportModal(btn.dataset.msgId);
            }
        });

        function showReportModal(id) {
            document.getElementById('report-msg-id').value = id;
            const modal = new bootstrap.Modal(document.getElementById('reportModal'));
            modal.show();
        }

        document.getElementById('report-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const id = formData.get('message_id');

            try {
                await axios.post(`/ai-chat/message/${id}/report`, {
                    reason: formData.get('reason'),
                    details: formData.get('details')
                });
                alert('Report submitted successfully.');
                bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
            } catch (error) {
                alert('Failed to submit report.');
            }
        });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endsection