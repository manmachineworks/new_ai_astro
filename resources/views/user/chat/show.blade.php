@extends('layouts.user')

@section('header')
    <div class="d-md-none">
        <x-ui.page-header title="Chat" :breadcrumbs="[['label' => 'Chats', 'url' => route('user.chat.index')], ['label' => $activeSession['astrologer_name'] ?? 'Astrologer']]" />
    </div>
    <div class="d-none d-md-block">
        <x-ui.page-header title="My Chats" description="Your conversation history with astrologers." />
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden" style="height: calc(100vh - 250px); min-height: 550px;">
        <x-user.chat.layout>
            <x-slot:sidebar>
                <div class="d-none d-md-block h-100">
                    <x-user.chat.chat-list :sessions="$sessions" :activeSessionId="$activeSession['id'] ?? null" />
                </div>
            </x-slot:sidebar>

            <div class="flex-grow-1 h-100">
                <x-user.chat.chat-window :session="$activeSession" :messages="$messages" />
            </div>
        </x-user.chat.layout>
    </div>
@endsection