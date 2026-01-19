@props(['trigger' => 'open-booking-modal', 'walletBalance', 'cost'])

<div x-data="{ open: false }" 
     x-on:{{ $trigger }}.window="open = true"
     x-show="open" 
     style="display: none;"
     class="fixed z-50 inset-0 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            
            <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $walletBalance >= $cost ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} sm:mx-0 sm:h-10 sm:w-10">
                         @if($walletBalance >= $cost)
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                         @else
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                         @endif
                    </div>
                    
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-white" id="modal-title">
                            Confirm Appointment
                        </h3>
                        <div class="mt-4 bg-zinc-50 dark:bg-zinc-700/50 p-3 rounded-lg text-sm text-zinc-600 dark:text-zinc-300 space-y-2">
                             <div class="flex justify-between">
                                 <span>Booking Fee</span>
                                 <span class="font-bold">₹{{ $cost }}</span>
                             </div>
                             <div class="flex justify-between">
                                 <span>Wallet Balance</span>
                                 <span class="{{ $walletBalance < $cost ? 'text-red-500' : 'text-green-600' }}">₹{{ $walletBalance }}</span>
                             </div>
                        </div>

                        @if($walletBalance < $cost)
                            <p class="mt-4 text-sm text-red-600">
                                Insufficient balance. Please recharge to proceed.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-zinc-50 dark:bg-zinc-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                @if($walletBalance >= $cost)
                    <button type="submit" form="booking-form"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm & Pay ₹{{ $cost }}
                    </button>
                    <button type="button" @click="open = false" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-800 text-base font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                @else
                    <a href="{{ route('wallet.recharge') }}"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Recharge Now
                    </a>
                     <button type="button" @click="open = false" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-800 text-base font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Back
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
