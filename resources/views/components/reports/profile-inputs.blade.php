@props(['user', 'editable' => true])

<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Your Birth Details</h3>
        @if($editable)
            <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">Edit Details</button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Date of Birth --}}
        <div>
            <label class="block text-xs text-zinc-500 uppercase tracking-wide">Date of Birth</label>
            <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                {{ \Carbon\Carbon::parse($user['dob'] ?? '1990-01-01')->format('d M, Y') }}
            </p>
        </div>

        {{-- Time of Birth --}}
        <div>
            <label class="block text-xs text-zinc-500 uppercase tracking-wide">Time of Birth</label>
            <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                {{ \Carbon\Carbon::parse($user['tob'] ?? '12:00:00')->format('h:i A') }}
            </p>
        </div>

        {{-- Place of Birth --}}
        <div>
            <label class="block text-xs text-zinc-500 uppercase tracking-wide">Place of Birth</label>
            <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-white">
                {{ $user['pob'] ?? 'New Delhi, India' }}
            </p>
        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-400">
        These details are used to generate your personalized horoscope and reports.
    </div>
</div>