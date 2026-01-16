@extends('layouts.app')

@section('header')
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore-compat.js"></script>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card chat-card shadow-lg border-0 d-flex flex-column" style="height: 80vh;">
                    <!-- Chat Header -->
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                        <a href="{{ route('user.chats') }}" class="me-3 text-dark"><i class="bi bi-arrow-left fs-4"></i></a>
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 40px; height: 40px;">
                            {{ substr($session->astrologerProfile->display_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $session->astrologerProfile->display_name }}</h6>
                            <small class="text-success" id="presence-status">Online</small>
                        </div>
                        <div class="ms-auto">
                            <span
                                class="badge bg-light text-dark border small">₹{{ number_format($session->price_per_message, 2) }}/msg</span>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="card-body bg-light overflow-auto p-3" id="message-container">
                        <div class="text-center my-4">
                            <span class="badge bg-white text-muted px-3 py-2 border small shadow-sm">Chat is real-time and
                                secure</span>
                        </div>
                        <div id="messages-list">
                            <!-- Messages populated by JS -->
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="card-footer bg-white border-top p-3">
                        <form id="chat-form" class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-circle"
                                style="width: 42px; height: 42px;"><i class="bi bi-plus-lg"></i></button>
                            <input type="text" id="message-input"
                                class="form-control rounded-pill border-light bg-light px-3" placeholder="Type a message..."
                                autocomplete="off">
                            <button type="submit" id="send-btn" class="btn btn-primary rounded-circle"
                                style="width: 42px; height: 42px;"><i class="bi bi-send-fill"></i></button>
                        </form>
                        <div id="typing-indicator" class="small text-muted mt-1 px-3 d-none">Astrologer is typing...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-card {
            border-radius: 12px;
        }

        #message-container::-webkit-scrollbar {
            width: 4px;
        }

        #message-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .msg-bubble {
            max-width: 80%;
            padding: 8px 14px;
            border-radius: 18px;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .msg-me {
            background-color: #0084ff;
            color: white;
            border-bottom-right-radius: 4px;
            align-self: flex-end;
        }

        .msg-them {
            background-color: white;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            align-self: flex-start;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .msg-time {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-top: 4px;
            display: block;
        }
    </style>

    <script>
        // Firebase Config
        const firebaseConfig = {
            apiKey: "{{ config('firebase.api_key') }}", // Optional for custom token, but needed for some SDK features
            projectId: "{{ config('firebase.project_id') }}"
        };

        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();
        const auth = firebase.auth();
        const conversationId = "{{ $session->conversation_id }}";
        const sessionId = "{{ $session->id }}";
        const myUid = "user_{{ auth()->id() }}";

        // 1. Auth with Custom Token
        fetch("{{ route('firebase.token') }}")
            .then(res => res.json())
            .then(data => auth.signInWithCustomToken(data.firebase_token))
            .then(() => {
                console.log("Logged in to Firebase");
                listenForMessages();
            })
            .catch(err => alert("Chat authentication failed: " + err.message));

        // 2. Listen for Messages
        function listenForMessages() {
            db.collection('conversations').doc(conversationId)
                .collection('messages')
                .orderBy('sentAt', 'asc')
                .onSnapshot(snapshot => {
                    snapshot.docChanges().forEach(change => {
                        if (change.type === "added") {
                            appendMessage(change.doc.data());
                        }
                    });
                });
        }

        function appendMessage(data) {
            const list = document.getElementById('messages-list');
            const isMe = data.senderKey === myUid;
            const div = document.createElement('div');
            div.className = `d-flex flex-column ${isMe ? 'align-items-end' : 'align-items-start'} mb-2`;

            let content = data.text;
            if (data.type === 'image') {
                content = `<img src="${data.mediaUrl}" class="img-fluid rounded mb-1" style="max-width: 200px;">`;
            } else if (data.type === 'file') {
                content = `<a href="${data.mediaUrl}" target="_blank" class="text-white"><i class="bi bi-file-earmark"></i> ${data.fileName || 'Attachment'}</a>`;
            }

            const time = data.sentAt ? new Date(data.sentAt.seconds * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '...';

            div.innerHTML = `
                <div class="msg-bubble ${isMe ? 'msg-me' : 'msg-them'}">
                    ${content}
                    <span class="msg-time text-end">${time}</span>
                </div>
            `;
            list.appendChild(div);
            scrollToBottom();
        }

        function scrollToBottom() {
            const container = document.getElementById('message-container');
            container.scrollTop = container.scrollHeight;
        }

        // 3. Send Message logic
        const chatForm = document.getElementById('chat-form');
        chatForm.onsubmit = async (e) => {
            e.preventDefault();
            const input = document.getElementById('message-input');
            const text = input.value.trim();
            if (!text) return;

            input.value = '';
            const btn = document.getElementById('send-btn');
            btn.disabled = true;

            try {
                // STEP 1: Authorize (Check Wallet)
                const authRes = await fetch(`/chats/${sessionId}/authorize-send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ text })
                });
                const authData = await authRes.json();

                if (!authData.allow) {
                    alert(authData.message || "Insufficient balance");
                    btn.disabled = false;
                    return;
                }

                // STEP 2: Write to Firestore
                const msgRef = db.collection('conversations').doc(conversationId).collection('messages').doc();
                await msgRef.set({
                    senderKey: myUid,
                    text: text,
                    type: 'text',
                    sentAt: firebase.firestore.FieldValue.serverTimestamp()
                });

                // STEP 3: Confirm Sent (Charge Wallet)
                await fetch(`/chats/${sessionId}/confirm-sent`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        firestore_message_id: msgRef.id,
                        auth_token: authData.auth_token
                    })
                });

            } catch (err) {
                console.error("Send failed", err);
                alert("Failed to send message.");
            } finally {
                btn.disabled = false;
            }
        };
    </script>
@endsection