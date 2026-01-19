@props(['type', 'value', 'verified' => false])

<div x-data="{ 
    mode: '{{ $verified ? 'verified' : 'unverified' }}', 
    otp: '',
    loading: false,
    
    sendOtp() {
        this.loading = true;
        // Mock API delay
        setTimeout(() => {
            this.loading = false;
            this.mode = 'sent';
            console.log('OTP Sent');
        }, 1500);
    },

    verifyOtp() {
        this.loading = true;
        // Mock Verification
        setTimeout(() => {
            this.loading = false;
            this.mode = 'verified';
            console.log('Verified');
        }, 1500);
    }
}" class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 capitalize">{{ $type }}
            Verification</label>

        <template x-if="mode === 'verified'">
            <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                Verified
            </span>
        </template>
        <template x-if="mode !== 'verified'">
            <span
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                Unverified
            </span>
        </template>
    </div>

    <div class="flex items-center space-x-2">
        <input type="text" value="{{ $value }}" readonly
            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-900 text-zinc-500 sm:text-sm">

        <template x-if="mode === 'unverified'">
            <button @click="sendOtp()" :disabled="loading"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                <span x-show="!loading">Verify</span>
                <span x-show="loading">Sending...</span>
            </button>
        </template>
    </div>

    {{-- OTP Input Area --}}
    <div x-show="mode === 'sent'" style="display: none;" class="mt-3">
        <div class="flex space-x-2">
            <input x-model="otp" type="text" placeholder="Enter OTP: 123456"
                class="flex-1 block w-full px-3 py-2 rounded-md border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 text-zinc-900 dark:text-white sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <button @click="verifyOtp()" :disabled="loading"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                <span x-show="!loading">Confirm</span>
                <span x-show="loading">...</span>
            </button>
        </div>
        <p class="mt-1 text-xs text-zinc-500">Mock OTP: Enter anything.</p>
    </div>
</div>