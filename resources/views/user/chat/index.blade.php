@extends('layouts.user')

@section('header')
    <x-ui.page-header title="My Chats" description="Your conversation history with astrologers." />
@endsection

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden" style="height: calc(100vh - 250px); min-height: 500px;">
        <x-user.chat.layout>
            <x-slot:sidebar>
                <x-user.chat.chat-list :sessions="$sessions" />
            </x-slot:sidebar>

            <div class="h-100 d-none d-md-flex flex-column align-items-center justify-content-center text-center p-5">
                <div class="bg-primary bg-opacity-10 p-4 rounded-circle mb-4">
                    <i class="bi bi-chat-dots text-primary fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark">Select a conversation</h4>
                <p class="text-muted mb-4 mx-auto" style="max-width: 350px;">
                    Choose a chat from the left to view messages or start a new consultation.
                </p>
                <a href="{{ route('user.astrologers.index') }}" class="btn btn-primary px-4">
                    Start New Chat
                </a>
            </div>
        </x-user.chat.layout>
    </div>
@endsection