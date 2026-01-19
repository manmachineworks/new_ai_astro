<div class="flex-1 flex flex-col h-full" x-data="{ 
        activeChat: {{ json_encode($activeSession ?? null) }},
        init() {
            $watch('activeChat', value => {
                if(value) {
                    // Start scroll to bottom
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        if(container) container.scrollTop = container.scrollHeight;
                    });
                }
            });
        }
     }" @open-chat.window="activeChat = $event.detail">
    {{-- Empty State --}}
    <div x-show="!activeChat" class="flex-1 flex flex-col items-center justify-center text-zinc-400 p-8">
        <div class="w-32 h-32 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
            <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                </path>
            </svg>
        </div>
        <p class="text-lg font-medium text-zinc-500">Select a chat to start messaging</p>
    </div>

    {{-- Chat Interface --}}
    <div x-show="activeChat" class="flex-1 flex flex-col h-full" style="display: none;">

        {{-- Header --}}
        <div
            class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 p-3 flex items-center justify-between shadow-sm z-10">
            <div class="flex items-center">
                <button @click="mobileChatOpen = false" class="md:hidden mr-3 text-zinc-500 hover:text-zinc-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <div class="relative">
                    <template x-if="activeChat?.image">
                        <img :src="activeChat.image" class="w-10 h-10 rounded-full object-cover">
                    </template>
                    <template x-if="!activeChat?.image">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            <span x-text="activeChat?.name?.charAt(0)"></span>
                        </div>
                    </template>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full">
                    </div>
                </div>

                <div class="ml-3">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-sm" x-text="activeChat?.name"></h3>
                    <p class="text-xs text-green-600">Online</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 text-zinc-500">
                <button class="hover:bg-zinc-100 p-2 rounded-full"><svg class="w-5 h-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg></button>
                <button class="hover:bg-zinc-100 p-2 rounded-full"><svg class="w-5 h-5" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                        </path>
                    </svg></button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-repeat"
            style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); opacity: 0.95;"
            x-ref="messageContainer">

            <div class="text-center my-4">
                <span
                    class="bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-xs px-3 py-1 rounded-full">Today</span>
            </div>

            {{-- Messages will be injected here via slot or loop. For Mockup we put sample bubbles --}}
            <x-chat.message-bubble type="received" text="Hello! How can I help you today?" time="10:30 AM" />
            <x-chat.message-bubble type="sent" text="Hi Astro Priya, I wanted to ask about my career prospects."
                time="10:31 AM" status="read" />
            <x-chat.message-bubble type="received" text="Sure, please share your birth details." time="10:32 AM" />

        </div>

        {{-- Composer --}}
        <div class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-3 z-10">
            <div class="flex items-end space-x-2">
                <button class="p-2 text-zinc-500 hover:text-indigo-600 rounded-full hover:bg-zinc-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                        </path>
                    </svg>
                </button>

                <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center px-4 py-2">
                    <textarea rows="1" placeholder="Type a message..."
                        class="w-full bg-transparent border-none focus:ring-0 text-zinc-900 dark:text-white resize-none max-h-32 p-0"
                        style="min-height: 24px;"></textarea>
                    <button class="ml-2 text-zinc-400 hover:text-yellow-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </button>
                </div>

                <button
                    class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-md flex items-center justify-center">
                    <svg class="w-5 h-5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>