@extends('admin.layouts.app')

@section('title', 'Ticket #' . $ticket->id)
@section('page_title', 'Support Ticket')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        <small class="text-muted">Category: {{ ucfirst($ticket->category) }}</small>
                    </div>
                    <span
                        class="badge bg-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'resolved' ? 'success' : 'secondary') }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Messages Thread -->
                    <div class="conversation" style="max-height: 500px; overflow-y: auto;">
                        @foreach($ticket->messages()->orderBy('created_at')->get() as $message)
                            <div class="mb-3 {{ $message->sender_type == 'admin' ? 'text-end' : '' }}">
                                <div class="d-inline-block text-start" style="max-width: 70%;">
                                    <div
                                        class="p-3 rounded {{ $message->sender_type == 'admin' ? 'bg-primary text-white' : 'bg-light' }}">
                                        {{ $message->message }}
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        {{ $message->sender_type == 'admin' ? 'Admin' : $ticket->user->name }} •
                                        {{ $message->created_at->format('d M, H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($ticket->status != 'closed')
                        <!-- Reply Form -->
                        <hr>
                        <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Your Reply</label>
                                <textarea name="message" class="form-control" rows="4" required
                                    placeholder="Type your reply..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Ticket Info</h6>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> #{{ $ticket->id }}</p>
                    <p><strong>Created:</strong> {{ $ticket->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Updated:</strong> {{ $ticket->updated_at->diffForHumans() }}</p>
                    <p class="mb-0"><strong>Messages:</strong> {{ $ticket->messages()->count() }}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">User Info</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $ticket->user->name }}</p>
                    <p><strong>Phone:</strong> {{ $ticket->user->phone }}</p>
                    <p class="mb-0"><strong>Email:</strong> {{ $ticket->user->email ?? 'N/A' }}</p>
                </div>
            </div>

            @if($ticket->status != 'closed')
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <form method="POST" action="{{ route('admin.support.resolve', $ticket) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Mark as Resolved</button>
                        </form>
                        <form method="POST" action="{{ route('admin.support.close', $ticket) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary w-100">Close Ticket</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection