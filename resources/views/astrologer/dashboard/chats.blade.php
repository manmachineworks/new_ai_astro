@extends('layouts.astrologer')

@section('title', 'Chats')
@section('page-title', 'Live Chats')

@push('styles')
    <style>
        .chat-layout {
            height: calc(100vh - 100px);
        }

        .chat-sidebar {
            width: 340px;
            border-right: 1px solid #e2e8f0;
            background: white;
            display: flex;
            flex-direction: column;
        }

        .chat-main {
            flex: 1;
            background: #e5ddd5;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* WhatsApp bg color */

        /* Sidebar Items */
        .chat-item {
            cursor: pointer;
            transition: background 0.2s;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .chat-item:hover {
            background: #f5f6f6;
        }

        .chat-item.active {
            background: #f0f2f5;
            border-left: 3px solid #00a884;
        }

        .chat-item.pinned {
            background-color: #fbfbfb;
        }

        /* Message Bubbles */
        .message-stream {
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            background-repeat: repeat;
            opacity: 0.9;
        }

        .message-bubble {
            max-width: 65%;
            padding: 8px 12px;
            border-radius: 7.5px;
            position: relative;
            margin-bottom: 4px;
            box-shadow: 0 1px 0.5px rgba(0, 0, 0, 0.13);
            font-size: 14.2px;
            line-height: 19px;
        }

        .message-bubble.sent {
            background: #d9fdd3;
            align-self: flex-end;
            border-top-right-radius: 0;
        }

        .message-bubble.received {
            background: white;
            align-self: flex-start;
            border-top-left-radius: 0;
        }

        .msg-meta {
            font-size: 11px;
            color: #667781;
            margin-top: 4px;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }

        /* Composer */
        .chat-composer {
            background: #f0f2f5;
            padding: 10px 16px;
            align-items: center;
            gap: 10px;
        }

        .composer-input {
            background: white;
            border-radius: 8px;
            padding: 9px 12px;
            border: none;
            width: 100%;
            outline: none;
            max-height: 100px;
            overflow-y: auto;
        }

        /* Quick Replies */
        .quick-replies {
            padding: 8px 16px;
            background: #f0f2f5;
            overflow-x: auto;
            white-space: nowrap;
            border-bottom: 1px solid #e0e0e0;
        }

        .quick-chip {
            display: inline-block;
            background: white;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 13px;
            color: #54656f;
            cursor: pointer;
            border: 1px solid #e0e0e0;
            margin-right: 8px;
        }

        .quick-chip:hover {
            background: #f5f6f6;
        }

        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
                position: absolute;
                z-index: 10;
                height: 100%;
            }

            .chat-main {
                position: absolute;
                width: 100%;
                height: 100%;
                transform: translateX(100%);
                transition: transform 0.3s;
                z-index: 20;
            }

            .chat-main.show {
                transform: translateX(0);
            }

            .chat-sidebar.hide {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="card card-premium overflow-hidden border-0 shadow-sm"
        style="height: calc(100vh - 110px); max-height: 800px;">
        <div class="d-flex h-100 position-relative text-dark">
            <!-- Sidebar -->
            <aside class="chat-sidebar" id="chatSidebar">
                <!-- Header -->
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <div class="fw-bold text-secondary">
                        <i class="fas fa-comments me-2"></i>Chats
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light rounded-circle" title="Status"><i
                                class="fas fa-circle-notch text-muted"></i></button>
                        <button class="btn btn-sm btn-light rounded-circle" title="New Chat"><i
                                class="fas fa-plus text-muted"></i></button>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="p-2 border-bottom">
                    <div class="input-group input-group-sm mb-2 px-2 pt-2">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end"
                            placeholder="Search or start new chat">
                    </div>
                    <div class="d-flex gap-2 px-2 pb-2 overflow-auto no-scrollbar">
                        <button class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-pill">All</button>
                        <button class="badge bg-light text-secondary border-0 px-3 py-2 rounded-pill">Unread</button>
                        <button class="badge bg-light text-secondary border-0 px-3 py-2 rounded-pill">Paid</button>
                        <button class="badge bg-light text-secondary border-0 px-3 py-2 rounded-pill">Groups</button>
                    </div>
                </div>

                <!-- Chat List -->
                <div class="flex-grow-1 overflow-auto custom-scrollbar">
                    <!-- Pinned Chat Mock -->
                    <div class="chat-item pinned d-flex gap-3" onclick="loadChat('pinned_1', this)">
                        <div class="position-relative">
                            <div class="avatar-circle-md bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                style="width:48px;height:48px;">
                                <i class="fas fa-thumbtack small"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-truncate fw-bold">Support Team</h6>
                                <span class="small text-muted" style="font-size: 11px;">Yesterday</span>
                            </div>
                            <p class="mb-0 small text-muted text-truncate mt-1">Ticket #9921 resolved.</p>
                        </div>
                    </div>

                    <!-- Dynamic List -->
                    @forelse($sessions as $session)
                        <div class="chat-item d-flex gap-3 {{ $loop->first ? 'active' : '' }}"
                            onclick="loadChat('{{ $session->id }}', this)">
                            <div class="position-relative">
                                <div class="avatar-circle-md bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="width:48px;height:48px;">
                                    {{ substr($session->user->name ?? 'Unknown', 0, 1) }}
                                </div>
                                @if($session->status == 'active')
                                    <span
                                        class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                                @endif
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-truncate fw-bold">
                                        {{ $session->user_masked_identifier ?? 'User #' . $session->user_id }}</h6>
                                    <span class="small {{ $session->unread_count > 0 ? 'text-success fw-bold' : 'text-muted' }}"
                                        style="font-size: 11px;">
                                        {{ $session->updated_at->format('h:i A') }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <p class="mb-0 small text-muted text-truncate flex-grow-1">
                                        @if($session->last_message_type == 'image') <i class="fas fa-camera small"></i> Photo
                                        @else {{ $session->last_message_preview ?? 'Tap to chat' }} @endif
                                    </p>
                                    @if($session->unread_count > 0)
                                        <span
                                            class="badge bg-success rounded-circle small d-flex align-items-center justify-content-center"
                                            style="width: 20px; height: 20px;">{{ $session->unread_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted mt-5">
                            <p class="small">No active conversations.</p>
                        </div>
                    @endforelse
                </div>
            </aside>

            <!-- Main Window -->
            <main class="chat-main" id="chatWindow">
                <!-- Header -->
                <header
                    class="p-2 px-3 bg-light border-bottom d-flex justify-content-between align-items-center shadow-sm z-1">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-link text-dark p-0 me-2 d-md-none" onclick="closeChat()"><i
                                class="fas fa-arrow-left"></i></button>
                        <div class="avatar-circle-sm bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center"
                            style="width:40px;height:40px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark" style="font-size: 15px;" id="headerUser">Select a Chat</span>
                            <span class="small text-muted" style="font-size: 12px; white-space: nowrap;"
                                id="headerStatus">click sidebar to start</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <!-- Wallet Badge -->
                        <div
                            class="d-none d-md-flex align-items-center gap-2 bg-white px-3 py-1 rounded-pill border shadow-sm">
                            <i class="fas fa-wallet text-warning"></i>
                            <span class="fw-bold small text-dark">₹ <span id="userWalletBalance">0.00</span></span>
                        </div>

                        <div class="d-flex gap-2 text-muted">
                            <button class="btn btn-link text-muted p-2"><i class="fas fa-search"></i></button>
                            <button class="btn btn-link text-muted p-2"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                </header>

                <!-- Messages Area -->
                <div class="flex-grow-1 overflow-auto custom-scrollbar message-stream p-3 p-md-5 d-flex flex-column"
                    id="messagesArea">
                    <!-- System Message -->
                    <div class="d-flex justify-content-center my-3">
                        <div
                            class="bg-warning-subtle text-warning-emphasis px-3 py-1 rounded shadow-sm small text-center border border-warning-subtle">
                            <i class="fas fa-lock small me-1"></i> Messages are end-to-end encrypted. No wallet deduction
                            for first 2 mins.
                        </div>
                    </div>

                    <!-- Received -->
                    <div class="message-bubble received">
                        <span class="text-break">Namaste Panditji, I need to ask about my marriage prospects. Can you
                            help?</span>
                        <div class="msg-meta">10:40 AM</div>
                    </div>

                    <!-- Sent (Paid) -->
                    <div class="message-bubble sent">
                        <span class="text-break">Hari Om! Yes, please share your birth details.</span>
                        <div class="msg-meta">
                            10:42 AM <i class="fas fa-check-double text-primary"></i> <i
                                class="fas fa-rupee-sign text-success small" title="Paid Message"
                                style="font-size: 8px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Replies -->
                <div class="quick-replies d-none d-md-block">
                    <span class="quick-chip">What is your DOB?</span>
                    <span class="quick-chip">Please share Place of Birth</span>
                    <span class="quick-chip">Checking your chart...</span>
                    <span class="quick-chip">Send Kundli</span>
                </div>

                <!-- Composer -->
                <footer class="chat-composer border-top d-flex">
                    <button class="btn btn-link text-muted p-1"><i class="far fa-smile fa-lg"></i></button>
                    <button class="btn btn-link text-muted p-1"><i class="fas fa-plus fa-lg"></i></button>

                    <div class="flex-grow-1 position-relative">
                        <!-- Low Balance Overlay (Example Logic) -->
                        <div id="lowBalanceOverlay"
                            class="d-none position-absolute top-0 start-0 w-100 h-100 bg-light opacity-75 d-flex align-items-center justify-content-center rounded z-2">
                            <span class="small fw-bold text-danger"><i class="fas fa-exclamation-circle"></i> Low
                                Balance</span>
                        </div>
                        <input type="text" class="composer-input" placeholder="Type a message" id="msgInput">
                    </div>

                    <button class="btn btn-link text-muted p-1" id="micBtn"><i class="fas fa-microphone fa-lg"></i></button>
                    <button class="btn btn-success rounded-circle shadow-sm d-none" id="sendBtn"
                        style="width: 40px; height: 40px;">
                        <i class="fas fa-paper-plane text-white"></i>
                    </button>
                </footer>
            </main>
        </div>
    </div>

    <script>
        const msgInput = document.getElementById('msgInput');
        const sendBtn = document.getElementById('sendBtn');
        const micBtn = document.getElementById('micBtn');

        msgInput.addEventListener('input', function () {
            if (this.value.trim().length > 0) {
                sendBtn.classList.remove('d-none');
                micBtn.classList.add('d-none');
            } else {
                sendBtn.classList.add('d-none');
                micBtn.classList.remove('d-none');
            }
        });

        function loadChat(sessionId, el) {
            if (window.innerWidth < 768) {
                document.getElementById('chatWindow').classList.add('show');
                document.getElementById('chatSidebar').classList.add('hide');
            }

            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');

            // Mock Data Update
            document.getElementById('headerUser').innerText = el.querySelector('h6').innerText;
            document.getElementById('headerStatus').innerText = 'Online';
            document.getElementById('userWalletBalance').innerText = '120.50';

            // Simulate low balance warning for demo
            if (sessionId === 'pinned_1') {
                document.getElementById('lowBalanceOverlay').classList.remove('d-none');
            } else {
                document.getElementById('lowBalanceOverlay').classList.add('d-none');
            }
        }

        function closeChat() {
            document.getElementById('chatWindow').classList.remove('show');
            document.getElementById('chatSidebar').classList.remove('hide');
        }
    </script>
@endsection