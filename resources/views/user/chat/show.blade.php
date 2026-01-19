@extends('layouts.user')

@section('header')
    <div class="md:hidden">
        <x-ui.page-header title="Chat" :breadcrumbs="[['label' => 'Chats', 'url' => route('user.chat.index')], ['label' => $activeSession['astrologer_name']]]" />
    </div>
    <div class="hidden md:block">
        <x-ui.page-header title="My Chats" description="Your conversation history with astrologers." />
    </div>
@endsection

@section('content')
    <div class="h-[calc(100vh-16rem)] min-h-[500px]">
        <x-user.chat.layout>
            <x-slot:sidebar>
                <div class="hidden md:block h-full">
                    <x-user.chat.chat-list :sessions="$sessions" :activeSessionId="$activeSession['id']" />
                </div>
                {{-- On mobile, sidebar is hidden in show view unless toggled --}}
            </x-slot:sidebar>

            <x-user.chat.chat-window :session="$activeSession" :messages="$messages" />
        </x-user.chat.layout>
    </div>
@endsection