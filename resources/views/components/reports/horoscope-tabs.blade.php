@props(['horoscope'])

<div x-data="{ tab: 'daily' }"
    class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-lg shadow-lg text-white overflow-hidden">
    {{-- Header / Tabs --}}
    <div class="flex text-sm font-medium bg-white/10 backdrop-blur-sm border-b border-white/10">
        <button @click="tab = 'daily'" :class="tab === 'daily' ? 'bg-white/20' : 'hover:bg-white/5'"
            class="flex-1 py-3 text-center transition focus:outline-none">
            Daily
        </button>
        <button @click="tab = 'weekly'" :class="tab === 'weekly' ? 'bg-white/20' : 'hover:bg-white/5'"
            class="flex-1 py-3 text-center transition focus:outline-none">
            Weekly
        </button>
        <button @click="tab = 'monthly'" :class="tab === 'monthly' ? 'bg-white/20' : 'hover:bg-white/5'"
            class="flex-1 py-3 text-center transition focus:outline-none">
            Monthly
        </button>
    </div>

    {{-- Content --}}
    <div class="p-6 min-h-[200px]">
        {{-- Daily --}}
        <div x-show="tab === 'daily'" class="animate-fade-in">
            <h4 class="text-lg font-bold mb-2">{{ now()->format('l, d M Y') }}</h4>
            <div class="space-y-4 text-indigo-100 leading-relaxed text-sm">
                <p>{{ $horoscope['daily'] ?? 'Stars are aligned in your favor today. Focus on your career goals.' }}</p>
            </div>
            <div class="mt-6 flex gap-4">
                <div class="bg-white/10 rounded-lg p-3 flex-1 text-center">
                    <div class="text-xs opacity-70 uppercase tracking-widest">Lucky Color</div>
                    <div class="font-bold mt-1">Royal Blue</div>
                </div>
                <div class="bg-white/10 rounded-lg p-3 flex-1 text-center">
                    <div class="text-xs opacity-70 uppercase tracking-widest">Lucky Number</div>
                    <div class="font-bold mt-1">7</div>
                </div>
            </div>
        </div>

        {{-- Weekly --}}
        <div x-show="tab === 'weekly'" class="animate-fade-in" style="display: none;">
            <h4 class="text-lg font-bold mb-2">This Week</h4>
            <div class="space-y-4 text-indigo-100 leading-relaxed text-sm">
                <p>{{ $horoscope['weekly'] ?? 'This week brings new opportunities in finance.' }}</p>
            </div>
        </div>

        {{-- Monthly --}}
        <div x-show="tab === 'monthly'" class="animate-fade-in" style="display: none;">
            <h4 class="text-lg font-bold mb-2">{{ now()->format('F Y') }}</h4>
            <div class="space-y-4 text-indigo-100 leading-relaxed text-sm">
                <p>{{ $horoscope['monthly'] ?? 'A transformative month ahead for relationships.' }}</p>
            </div>
        </div>
    </div>
</div>