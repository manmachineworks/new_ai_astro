@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Ticket #{{ $ticket['id'] }}" description="{{ $ticket['subject'] }}" :breadcrumbs="[['label' => 'Support', 'url' => route('user.support.index')], ['label' => '#' . $ticket['id']]]">
        <x-slot:actions>
            <x-ui.badge :color="$ticket['status'] === 'Open' ? 'success' : 'secondary'" :label="$ticket['status']" />
        </x-slot:actions>
    </x-ui.page-header>
@endsection

@section('content')
    <form action="{{ route('user.support.reply', $ticket['id']) }}" method="POST">
        @csrf
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Reply</label>
        <textarea name="reply" rows="3"
            class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white sm:text-sm mb-4"
            placeholder="Type your message..."></textarea>

        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Send Reply
            </button>
        </div>
    </form>
    </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <h4 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider mb-4">Ticket Details
            </h4>
            <div class="space-y-4 text-sm">
                <div>
                    <span class="block text-zinc-500">Category</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $ticket['category'] }}</span>
                </div>
                <div>
                    <span class="block text-zinc-500">Created On</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $ticket['created_at'] }}</span>
                </div>
                <div>
                    <span class="block text-zinc-500">Last Updated</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $ticket['updated_at'] }}</span>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection