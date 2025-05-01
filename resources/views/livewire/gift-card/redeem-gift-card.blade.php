<div class="max-w-xl mx-auto bg-white rounded-lg shadow-md overflow-hidden my-8">
    <div class="bg-amber-50 p-4 border-b border-amber-100">
        <h2 class="text-center text-xl font-bold text-amber-800">Redeem Your Gift Card</h2>
    </div>

    <div class="p-6">
        @if ($showForm)
            <div class="mb-4 text-gray-600">
                <p>Enter your gift card activation code below to add its value to your account balance.</p>
            </div>

            <form wire:submit="redeem" class="space-y-6">
                @if ($showError)
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="activation_key" class="block text-sm font-medium text-gray-700 mb-1">Activation Code</label>
                    <input
                        wire:model="activationKey"
                        type="text"
                        id="activation_key"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                        placeholder="Enter your activation code"
                    >
                    @error('activationKey') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="w-full px-4 py-2 bg-amber-600 text-white font-medium rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-not-allowed"
                    >
                        <span wire:loading.remove>Redeem Gift Card</span>
                        <span wire:loading>
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        @endif

        @if ($showSuccess)
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Gift Card Redeemed Successfully!</h3>

                <div class="mt-3 bg-green-50 border border-green-100 rounded-md p-4 mb-6">
                    <div class="flex flex-col items-center">
                        <span class="block text-2xl font-bold text-green-600">${{ number_format($giftCard->amount, 2) }}</span>
                        <span class="block text-sm text-green-700 mt-1">has been added to your account balance</span>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <div class="text-sm text-gray-700">
                        <p>Your new account balance is:</p>
                        <p class="text-lg font-bold mt-1">${{ number_format(auth()->user()->balanceFloat ?? 0, 2) }}</p>
                    </div>
                </div>

                <div class="mt-5 flex justify-center">
                    <a
                        href="{{ route('order') }}"
                        class="px-4 py-2 bg-amber-600 text-white font-medium rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors"
                    >
                        Place an Order
                    </a>
                </div>
            </div>
        @endif

        <!-- How It Works Section -->
        <div class="mt-10 border-t border-gray-200 pt-6">
            <h3 class="text-base font-semibold text-gray-900">How Gift Cards Work</h3>
            <div class="mt-3 space-y-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-600 font-semibold">1</div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Enter the activation code from your gift card</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-600 font-semibold">2</div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">The gift card amount will be added to your account balance</p>
                    </div>
                </div>
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-amber-100 text-amber-600 font-semibold">3</div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Use your balance to place orders in our café</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
