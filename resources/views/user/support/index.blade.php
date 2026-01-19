@extends('layouts.user')

@section('header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <x-ui.page-header title="Support Tickets" description="Track the status of your queries." />
        <a href="{{ route('user.support.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Create Ticket
        </a>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="list-group list-group-flush">
            @forelse($tickets as $ticket)
                <x-user.ticket-item :ticket="$ticket" />
            @empty
                <div class="p-5">
                    <x-ui.empty-state title="No tickets found" description="Need help? Create a new support ticket." />
                </div>
            @endforelse
        </div>

        @if(method_exists($tickets, 'links'))
            <div class="card-footer bg-white border-top p-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection