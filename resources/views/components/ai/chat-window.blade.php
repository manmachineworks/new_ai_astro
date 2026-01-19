@props(['price' => 15, 'walletBalance' => 0])

<div class="flex flex-col h-[calc(100vh-140px)] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-sm overflow-hidden"
    x-data="{ 
        messages: [{
            id: 1,
            type: 'bot',
            text: 'Namaste! I am your AI Astrology Assistant. Ask me anything about your horoscope, career, or relationships.',
            time: 'Now'
        }],
        input: '',
        isTyping: false,
        walletBalance: {{ $walletBalance }},
        price: {{ $price }},
        
        sendMessage() {
            if (this.input.trim() === '') return;
            
            if (this.walletBalance < this.price) {
                alert('Insufficient wallet balance to ask a question. Please recharge.');
                return;
            }

            // User Message
            this.messages.push({
                id: Date.now(),
                type: 'user',
                text: this.input,
                time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
            });

            this.walletBalance -= this.price; // Simulating deduction
            const userQuery = this.input;
            this.input = '';
            this.scrollToBottom();

            // Simulate Bot Response
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
                this.messages.push({
                    id: Date.now() + 1,
                    type: 'bot',
                    text: 'Based on your chart, ' + userQuery + ' looks promising. However, Mercury retrograde might cause some delays.',
                    time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                });
                this.scrollToBottom();
            }, 2000);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        }
     }">

    {{-- Pricing Banner --}}
    <x-ai.pricing-banner :price="$price" />

    {{-- Chat Area --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-zinc-50 dark:bg-zinc-950" x-ref="messagesContainer">
        <template x-for="msg in messages" :key="msg.id">
            <div class="flex w-full" :class="msg.type === 'user' ? 'justify-end' : 'justify-start'">
                <div class="flex max-w-[80%] md:max-w-[70%]">
                    {{-- Avatar for Bot --}}
                    <template x-if="msg.type === 'bot'">
                        <div class="flex-shrink-0 mr-3">
                            <div
                                class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 flex items-center justify-center text-white text-xs font-bold">
                                AI
                            </div>
                        </div>
                    </template>

                    <div :class="msg.type === 'user' ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-r-2xl rounded-tl-2xl shadow-sm border border-zinc-100 dark:border-zinc-800'"
                        class="px-4 py-3 relative">
                        <p class="text-sm leading-relaxed" x-text="msg.text"></p>
                        <span class="text-[10px] block mt-1 text-right opacity-70" x-text="msg.time"></span>
                    </div>

                    {{-- Avatar for User --}}
                    <template x-if="msg.type === 'user'">
                        {{-- No avatar for user in bubble line, just bubble right alignment --}}
                    </template>
                </div>
            </div>
        </template>

        {{-- Typing Indicator --}}
        <div x-show="isTyping" class="flex w-full justify-start" style="display: none;">
            <div
                class="flex items-center space-x-1 bg-white dark:bg-zinc-800 px-4 py-3 rounded-r-2xl rounded-tl-2xl shadow-sm border border-zinc-100 dark:border-zinc-800 ml-11">
                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce"></div>
                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    {{-- Input Area --}}
    <div class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-4">
        {{-- Chips --}}
        <div class="mb-3">
            <x-ai.prompt-chips />
        </div>

        <div class="flex items-end space-x-2">
            <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center px-4 py-2">
                <textarea x-model="input" x-ref="chatInput" @keydown.enter.prevent="sendMessage()" rows="1"
                    placeholder="Ask guidance regarding your future..."
                    class="w-full bg-transparent border-none focus:ring-0 text-zinc-900 dark:text-white resize-none max-h-32 p-0 text-sm"
                    style="min-height: 24px;"></textarea>
            </div>
            <button @click="sendMessage()"
                class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-md transition flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
        <p class="text-center text-xs text-zinc-400 mt-2">
            Wallet Balance: <span :class="walletBalance < price ? 'text-red-500' : 'text-green-600'"
                x-text="'₹' + walletBalance.toFixed(2)"></span>
        </p>
    </div>
</div>