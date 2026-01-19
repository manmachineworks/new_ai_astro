@props(['astrologer'])

<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow p-6 mb-6">
    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">Connect Now</h3>

    <div class="space-y-3" x-data>

        {{-- Call Option --}}
        <div
            class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:border-indigo-500 transition cursor-pointer group">
            <div class="flex items-center">
                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-full mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Audio Call</p>
                    <p class="text-xs text-green-600 font-medium">Available</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-zinc-900 dark:text-white">₹{{ $astrologer['price_per_min'] }}<span
                        class="text-xs font-normal text-zinc-500">/min</span></p>
                <button
                    @click="$dispatch('check-wallet', { price: {{ $astrologer['price_per_min'] }}, type: 'call', name: '{{ $astrologer['name'] }}' })"
                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Call Now &rarr;</button>
            </div>
        </div>

        {{-- Chat Option --}}
        <div
            class="flex items-center justify-between p-3 border border-zinc-200 dark:border-zinc-800 rounded-lg hover:border-indigo-500 transition cursor-pointer group">
            <div class="flex items-center">
                <div class="bg-green-100 text-green-600 p-2 rounded-full mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Chat</p>
                    <p class="text-xs text-green-600 font-medium">Available</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-zinc-900 dark:text-white">
                    ₹{{ $astrologer['chat_price_per_min'] ?? $astrologer['price_per_min'] }}<span
                        class="text-xs font-normal text-zinc-500">/min</span></p>
                <button
                    @click="$dispatch('check-wallet', { price: {{ $astrologer['chat_price_per_min'] ?? $astrologer['price_per_min'] }}, type: 'chat', name: '{{ $astrologer['name'] }}' })"
                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Chat Now &rarr;</button>
            </div>
        </div>

    </div>
</div>