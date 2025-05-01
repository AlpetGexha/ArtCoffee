<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden md:max-w-3xl my-8">
    <div class="md:flex">
        <div class="hidden md:block md:flex-shrink-0">
            <img class="h-full w-48 object-cover" src="{{ asset('images/gift-card.jpg') }}" alt="Gift Card" onerror="this.src='https://images.unsplash.com/photo-1549465220-1a8b9238cd48?q=80&w=300&h=600&fit=crop'">
        </div>

        <div class="p-6 w-full">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2 sm:mb-0">Send a Gift Card</h2>
                <span class="inline-block bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold">
                    Your Balance: ${{ auth()->check() ? number_format(auth()->user()->balanceFloat, 2) : '0.00' }}
                </span>
            </div>

            @if (!$showSuccessMessage)
                <form wire:submit="send" class="space-y-6">
                    <!-- Amount Selection -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input
                                wire:model="amount"
                                type="number"
                                id="amount"
                                min="10"
                                max="1000"
                                step="0.01"
                                class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                                placeholder="25.00"
                            >
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button
                                type="button"
                                x-data
                                @click="$wire.amount = 25"
                                class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium text-gray-800 hover:bg-gray-200 transition-colors"
                            >
                                $25
                            </button>
                            <button
                                type="button"
                                x-data
                                @click="$wire.amount = 50"
                                class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium text-gray-800 hover:bg-gray-200 transition-colors"
                            >
                                $50
                            </button>
                            <button
                                type="button"
                                x-data
                                @click="$wire.amount = 100"
                                class="px-3 py-1 bg-gray-100 rounded-full text-sm font-medium text-gray-800 hover:bg-gray-200 transition-colors"
                            >
                                $100
                            </button>
                        </div>
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Recipient Email -->
                    <div>
                        <label for="recipient_email" class="block text-sm font-medium text-gray-700 mb-1">Recipient Email</label>
                        <input
                            wire:model="recipientEmail"
                            type="email"
                            id="recipient_email"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                            placeholder="friend@example.com"
                        >
                        @error('recipientEmail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Occasion Selection -->
                    <div>
                        <label for="occasion" class="block text-sm font-medium text-gray-700 mb-1">Occasion (Optional)</label>
                        <select
                            wire:model="occasion"
                            id="occasion"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                        >
                            <option value="">Select an occasion</option>
                            @foreach ($occasions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('occasion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Personal Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Personal Message (Optional)</label>
                        <textarea
                            wire:model="message"
                            id="message"
                            rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                            placeholder="Write a personal message..."
                        ></textarea>
                        @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-500">Maximum 200 characters</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-amber-600 text-white font-medium rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                        >
                            <span wire:loading.remove>Send Gift Card</span>
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
            @else
                <!-- Success Message -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-green-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Gift Card Sent Successfully!</h3>
                    <p class="text-gray-600 mb-6">Your gift card has been sent to {{ $createdGiftCard?->recipient_email }}</p>

                    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-left text-gray-500">Amount:</div>
                            <div class="text-right font-medium">${{ number_format($createdGiftCard?->amount, 2) }}</div>

                            <div class="text-left text-gray-500">Activation Code:</div>
                            <div class="text-right font-mono text-xs bg-gray-100 p-1 rounded">{{ $createdGiftCard?->activation_key }}</div>

                            @if ($createdGiftCard?->occasion)
                                <div class="text-left text-gray-500">Occasion:</div>
                                <div class="text-right">{{ ucfirst($createdGiftCard?->occasion) }}</div>
                            @endif

                            <div class="text-left text-gray-500">Expires:</div>
                            <div class="text-right">{{ $createdGiftCard?->expires_at?->format('M j, Y') }}</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button
                            wire:click="$set('showSuccessMessage', false)"
                            class="px-4 py-2 bg-amber-600 text-white font-medium rounded-md hover:bg-amber-700 transition-colors"
                        >
                            Send Another Gift Card
                        </button>
                        <a
                            href="{{ route('home') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50 transition-colors"
                        >
                            Return to Homepage
                        </a>
                    </div>
                </div>
            @endif

            <!-- Information Panel -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4 text-sm text-blue-700">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium">About Gift Cards</h3>
                        <div class="mt-2">
                            <p>Gift cards are valid for one year from the date of purchase and can only be redeemed once.</p>
                            <p class="mt-1">The recipient will receive an email with instructions on how to redeem their gift card.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
