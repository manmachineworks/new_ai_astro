<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">My Dashboard</h1>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in as {{ Auth::user()->name }}!

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border p-4 rounded-lg">
                            <h3 class="font-bold mb-2">Wallet Balance</h3>
                            <p class="text-2xl text-green-600">
                                ₹{{ number_format(Auth::user()->wallet->balance ?? 0, 2) }}</p>
                            <a href="{{ route('user.wallet') }}"
                                class="mt-2 inline-block text-indigo-600 hover:text-indigo-800">Recharge Wallet
                                &rarr;</a>
                        </div>
                        <div class="border p-4 rounded-lg">
                            <h3 class="font-bold mb-2">Upcoming Sessions</h3>
                            <p class="text-gray-500">No upcoming sessions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>