@props(['astrologer'])

<div class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow overflow-hidden mb-6">
    {{-- Cover Pattern --}}
    <div class="h-32 bg-gradient-to-r from-purple-500 to-indigo-600 opacity-90"></div>

    <div class="px-6 pb-6">
        <div class="relative flex flex-col md:flex-row items-start md:items-end -mt-12 mb-4">
            {{-- Avatar --}}
            <div class="relative">
                @if($astrologer['profile_image'])
                    <img class="w-32 h-32 rounded-full border-4 border-white dark:border-zinc-900 shadow-lg object-cover"
                        src="{{ $astrologer['profile_image'] }}" alt="{{ $astrologer['name'] }}">
                @else
                    <div
                        class="w-32 h-32 rounded-full bg-indigo-50 border-4 border-white dark:border-zinc-900 shadow-lg flex items-center justify-center text-4xl text-indigo-600 font-bold">
                        {{ substr($astrologer['name'], 0, 1) }}
                    </div>
                @endif

                @if($astrologer['online'])
                    <div class="absolute bottom-2 right-2 w-6 h-6 bg-green-500 border-4 border-white dark:border-zinc-900 rounded-full"
                        title="Online"></div>
                @endif
            </div>

            {{-- Name & Stats --}}
            <div class="mt-4 md:mt-0 md:ml-6 flex-1">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <div class="flex items-center">
                            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white mr-2">{{ $astrologer['name'] }}
                            </h1>
                            @if($astrologer['verified'] ?? true)
                                <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="Verified">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812zM7.44 8.707l-1.414 1.414L9 13.12l5.657-5.657-1.414-1.414L9 10.293 7.44 8.707z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">
                            {{ is_array($astrologer['specialties']) ? implode(', ', $astrologer['specialties']) : $astrologer['specialties'] }}
                        </p>
                        <p class="text-zinc-400 text-sm mt-0.5">
                            Speaks:
                            {{ is_array($astrologer['languages'] ?? []) ? implode(', ', $astrologer['languages']) : 'English, Hindi' }}
                        </p>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center space-x-6 mt-4 md:mt-0">
                        <div class="text-center">
                            <span
                                class="block text-xl font-bold text-zinc-900 dark:text-white">{{ $astrologer['rating'] }}</span>
                            <span class="text-xs text-zinc-500">Rating</span>
                        </div>
                        <div class="text-center border-l border-zinc-200 dark:border-zinc-700 pl-6">
                            <span
                                class="block text-xl font-bold text-zinc-900 dark:text-white">{{ $astrologer['consultations'] ?? '2K+' }}</span>
                            <span class="text-xs text-zinc-500">Orders</span>
                        </div>
                        <div class="text-center border-l border-zinc-200 dark:border-zinc-700 pl-6">
                            <span
                                class="block text-xl font-bold text-zinc-900 dark:text-white">{{ $astrologer['experience'] ?? '5+' }}
                                Yr</span>
                            <span class="text-xs text-zinc-500">Exp.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 border-t border-zinc-100 dark:border-zinc-800 pt-4">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-2">About Me</h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                {{ $astrologer['bio'] ?? 'Experienced Astrologer with over 5 years of expertise in Vedic Astrology and Numerology. I have helped thousands of clients find clarity in their love life, career, and personal growth.' }}
            </p>
        </div>
    </div>
</div>