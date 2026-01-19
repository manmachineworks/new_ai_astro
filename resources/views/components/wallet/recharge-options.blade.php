@props(['walletBalance'])

<div x-data="{ amount: 500 }">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
        <h2 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Add Money to Wallet</h2>

        <p class="text-sm text-zinc-500 mb-6">Current Balance: <span
                class="font-bold text-zinc-900 dark:text-white">₹{{ number_format($walletBalance, 2) }}</span></p>

        {{-- Presets --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            @foreach([100, 200, 500, 1000, 2000, 5000] as $preset)
                <button @click="amount = {{ $preset }}"
                    :class="{'bg-indigo-600 text-white ring-2 ring-indigo-500 ring-offset-2': amount == {{ $preset }}, 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700': amount != {{ $preset }}}"
                    class="py-3 px-4 rounded-md text-sm font-bold shadow-sm focus:outline-none transition-all">
                    ₹{{ $preset }}
                </button>
            @endforeach
        </div>

        {{-- Custom Input --}}
        <form action="{{ route('user.wallet.initiate') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Or Enter
                    Custom Amount</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-zinc-500 sm:text-sm">₹</span>
                    </div>
                    <input type="number" name="amount" id="amount" x-model="amount" min="1" step="1" required
                        class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-lg border-zinc-300 dark:border-zinc-700 rounded-md py-3 dark:bg-zinc-800 dark:text-white"
                        placeholder="0.00">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-zinc-500 sm:text-sm">INR</span>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Proceed to Pay ₹<span x-text="amount"></span>
            </button>
            <p class="mt-4 text-xs text-center text-zinc-500">
                Secured by PhonePe
            </p>
        </form>
    </div>
</div>