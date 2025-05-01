<div class="min-h-screen bg-gray-100">
    <!-- Header Section -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-900">Make Your Own Coffee</h1>
            <button
                class="relative flex items-center px-4 py-2 bg-amber-600 text-white font-medium rounded-lg"
                x-data="{ cartCount: 0 }"
                x-init="$wire.$on('cart-updated', () => { cartCount = $wire.cart.length })"
                @click="$dispatch('toggle-cart')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Cart
                <span
                    x-show="cartCount > 0"
                    x-text="cartCount"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs"
                ></span>
            </button>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Main Content -->
        <div x-data="{
            showCart: false,
            showCustomization: false,
            showCheckout: false
        }" class="relative">

            <!-- Products List - Shown when not customizing -->
            <div x-show="!showCustomization && !showCart && !showCheckout" class="space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Select Your Items</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($products as $product)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                                        <p class="text-gray-600 text-sm line-clamp-2">{{ $product->description }}</p>
                                    </div>
                                    <p class="font-semibold text-amber-600">${{ number_format($product->base_price, 2) }}</p>
                                </div>

                                <div class="mt-4 flex justify-between">
                                    <button
                                        wire:click="startCustomizing({{ $product->id }})"
                                        @click="showCustomization = true"
                                        class="px-4 py-2 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700"
                                    >
                                        Customize
                                    </button>
                                    <button
                                        wire:click="addProductToCart({{ $product->id }})"
                                        class="px-4 py-2 border border-amber-600 text-amber-600 font-medium rounded-lg transition-all hover:bg-amber-50"
                                    >
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center">
                            <p class="text-gray-500">No products available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Customization Panel - Shown when customizing a product -->
            <div
                x-show="showCustomization"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4"
                class="bg-white rounded-lg shadow-lg p-6"
            >
                @if ($currentProduct)
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Customize Your {{ $currentProduct->name }}</h2>
                        <button
                            wire:click="resetCustomization"
                            @click="showCustomization = false"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Dynamic customization options - Fetch from database -->
                        @php
                            $options = App\Models\ProductOption::where('product_id', $currentProduct->id)
                                ->where('is_available', true)
                                ->orderBy('option_category')
                                ->orderBy('display_order')
                                ->get()
                                ->groupBy('option_category');
                        @endphp

                        @forelse ($options as $category => $categoryOptions)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ ucfirst($category) }}</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($categoryOptions as $option)
                                        <label class="flex items-center space-x-2 p-2 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                               :class="{'bg-amber-50 border-amber-500': $wire.customizations[{{ $currentProduct->id }}] && $wire.customizations[{{ $currentProduct->id }}]['{{ $category }}'] == {{ $option->id }}}"
                                        >
                                            <input
                                                type="radio"
                                                name="option_{{ $category }}"
                                                value="{{ $option->id }}"
                                                wire:click="setCustomization({{ $currentProduct->id }}, '{{ $category }}', {{ $option->id }})"
                                                class="text-amber-600 focus:ring-amber-500"
                                                :checked="$wire.customizations[{{ $currentProduct->id }}] && $wire.customizations[{{ $currentProduct->id }}]['{{ $category }}'] == {{ $option->id }}"
                                            >
                                            <div>
                                                <span class="font-medium">{{ $option->option_name }}</span>
                                                @if ($option->additional_price > 0)
                                                    <span class="text-sm text-amber-600 ml-1">+${{ number_format($option->additional_price, 2) }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No customization options available for this product.</p>
                        @endforelse

                        <!-- Special Instructions -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Special Instructions</h3>
                            <textarea
                                wire:model="specialInstructions"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                                rows="2"
                                placeholder="Any special requests? (e.g., extra hot, less ice)"
                            ></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button
                                wire:click="addToCart"
                                @click="showCustomization = false"
                                class="px-6 py-3 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Shopping Cart - Toggleable -->
            <div
                x-show="showCart"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4"
                @toggle-cart.window="showCart = !showCart"
                class="bg-white rounded-lg shadow-lg p-6"
            >
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Your Cart</h2>
                    <button
                        @click="showCart = false"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if (count($cart) > 0)
                    <div class="space-y-4 mb-6">
                        @foreach ($cart as $index => $item)
                            <div class="flex items-center space-x-4 py-3 border-b">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $item['product_name'] }}</h4>

                                    @if (!empty($item['customizations']))
                                        <div class="text-sm text-gray-600 mt-1">
                                            <span class="font-medium">Customizations:</span>
                                            <ul class="ml-2">
                                                @foreach ($item['customizations'] as $category => $optionId)
                                                    @php
                                                        $option = App\Models\ProductOption::find($optionId);
                                                    @endphp
                                                    @if ($option)
                                                        <li>{{ ucfirst($category) }}: {{ $option->option_name }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if ($item['special_instructions'])
                                        <div class="text-sm text-gray-600 mt-1">
                                            <span class="font-medium">Special Instructions:</span>
                                            <p class="ml-2">{{ $item['special_instructions'] }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center space-x-3">
                                    <button wire:click="updateQuantity({{ $index }}, -1)" class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="font-medium">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $index }}, 1)" class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-right">
                                    <div class="font-medium">${{ number_format($item['total_price'], 2) }}</div>
                                    <button wire:click="removeFromCart({{ $index }})" class="text-xs text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between text-lg font-semibold mb-6">
                            <span>Subtotal:</span>
                            <span>${{ number_format($cartTotal, 2) }}</span>
                        </div>

                        <div class="flex flex-col space-y-4">
                            <button
                                @click="showCart = false; showCheckout = true"
                                class="w-full px-6 py-3 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700"
                            >
                                Proceed to Checkout
                            </button>
                            <button
                                @click="showCart = false"
                                class="w-full px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg transition-all hover:bg-gray-50"
                            >
                                Continue Shopping
                            </button>
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-gray-500 mb-4">Your cart is empty</p>
                        <button
                            @click="showCart = false"
                            class="px-6 py-2 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700"
                        >
                            Browse Products
                        </button>
                    </div>
                @endif
            </div>

            <!-- Checkout Form -->
            <div
                x-show="showCheckout"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4"
                class="bg-white rounded-lg shadow-lg p-6"
            >
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Finalize Your Order</h2>
                    <button
                        @click="showCheckout = false; showCart = true"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span>${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (10%):</span>
                                <span>${{ number_format($cartTotal * 0.1, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-medium">
                                <span>Total:</span>
                                <span>${{ number_format($cartTotal + ($cartTotal * 0.1), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Redemption Type -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Pickup Option</h3>
                        <div class="flex space-x-4">
                            <label class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                   :class="{'bg-amber-50 border-amber-500': $wire.redemptionType === 'in-store'}"
                            >
                                <input
                                    type="radio"
                                    name="redemptionType"
                                    value="in-store"
                                    wire:model="redemptionType"
                                    class="text-amber-600 focus:ring-amber-500"
                                >
                                <span class="font-medium">In-Store Pickup</span>
                            </label>
                            <label class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                   :class="{'bg-amber-50 border-amber-500': $wire.redemptionType === 'online'}"
                            >
                                <input
                                    type="radio"
                                    name="redemptionType"
                                    value="online"
                                    wire:model="redemptionType"
                                    class="text-amber-600 focus:ring-amber-500"
                                >
                                <span class="font-medium">Order Online</span>
                            </label>
                        </div>
                    </div>

                    <!-- Points Redemption -->
                    @if (auth()->check() && auth()->user()->available_points > 0)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Redeem Points</h3>
                            <p class="text-gray-600 mb-2">You have {{ auth()->user()->available_points }} points available.</p>
                            <div class="flex items-center space-x-4">
                                <input
                                    type="number"
                                    min="0"
                                    max="{{ auth()->user()->available_points }}"
                                    wire:model="pointsToRedeem"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50 w-24"
                                >
                                <span class="text-gray-600">points = ${{ number_format($pointsToRedeem * 0.1, 2) }} discount</span>
                            </div>
                        </div>
                    @endif

                    <!-- Personal Message -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Personal Message</h3>
                        <textarea
                            wire:model="personalMessage"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                            rows="3"
                            placeholder="Add a personal message for your order (optional)"
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            wire:click="placeOrder"
                            class="px-6 py-3 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700"
                        >
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
