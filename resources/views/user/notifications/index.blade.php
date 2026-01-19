@extends('layouts.user')

@section('header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <x-ui.page-header title="Notifications" />
        <form action="{{ route('user.notifications.markAllRead') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link text-primary text-decoration-none fw-medium p-0">
                <i class="bi bi-check2-all me-1"></i>Mark all as read
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm mx-auto overflow-hidden" style="max-width: 900px;">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <x-user.notification-item :notification="$notification" />
            @empty
                <div class="p-5">
                    <x-ui.empty-state title="No notifications" description="You're all caught up! Check back later." />
                </div>
            @endforelse
        </div>

        @if(method_exists($notifications, 'links'))
            <div class="card-footer bg-white border-top p-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection