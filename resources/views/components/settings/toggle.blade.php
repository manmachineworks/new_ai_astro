@props(['enabled' => false, 'label' => '', 'description' => ''])

<div x-data="{ on: {{ $enabled ? 'true' : 'false' }} }" class="flex items-center justify-between py-4">
    <div class="flex flex-col flex-grow">
        <span class="text-sm font-medium text-zinc-900 dark:text-white" id="availability-label">
            {{ $label }}
        </span>
        @if($description)
            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $description }}
            </span>
        @endif
    </div>
    <button type="button" @click="on = !on" :class="on ? 'bg-indigo-600' : 'bg-zinc-200 dark:bg-zinc-700'"
        class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        role="switch">
        <span aria-hidden="true" :class="on ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
    </button>
</div>