@props(['required', 'balance', 'action', 'route'])

@php
    $insufficient = $balance < $required;
@endphp

<div x-data="{ openGate: false }">
    <div @click="
        if({{ $insufficient ? 'true' : 'false' }}) { 
            $event.preventDefault(); 
            openGate = true; 
        }
    ">
        {{ $slot }}
    </div>

    @if($insufficient)
        <x-ui.modal name="wallet-gate-{{ Str::random(5) }}" :show="false" x-model="openGate" title="Low Balance">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold leading-6 text-zinc-900 dark:text-zinc-100" id="modal-title">
                        Insufficient Balance</h3>
                    <div class="mt-2">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            You need at least <strong>₹{{ $required }}</strong> to {{ $action }}. Your current balance is
                            <strong>₹{{ $balance }}</strong>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6">
                <a href="{{ route('user.wallet.recharge') }}"
                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Recharge Wallet
                </a>
                <button type="button" @click="openGate = false"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100 shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 sm:col-start-1 sm:mt-0">
                    Cancel
                </button>
            </div>
        </x-ui.modal>
    @endif
</div>