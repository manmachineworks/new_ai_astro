@props(['title', 'status' => 'pending'])

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        'missing' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300',
    ];
    $statusColor = $statusColors[$status] ?? $statusColors['missing'];
    $iconMap = [
        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'approved' => 'M5 13l4 4L19 7',
        'rejected' => 'M6 18L18 6M6 6l12 12',
        'missing' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
    ];
@endphp

<div
    class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
    <div class="flex items-center space-x-4">
        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
            <svg class="h-5 w-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $title }}</p>
            <span
                class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium capitalize {{ $statusColor }}">
                {{ $status }}
            </span>
        </div>
    </div>

    <div>
        @if($status === 'missing' || $status === 'rejected')
            <button class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">Upload</button>
        @elseif($status === 'pending')
            <span class="text-zinc-400 text-xs italic">In Review</span>
        @else
            <span class="text-green-600 text-xs font-bold">Done</span>
        @endif
    </div>
</div>