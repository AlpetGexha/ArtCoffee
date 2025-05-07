<div class="min-h-screen bg-gray-100">
    <!-- Header Section - Optimized for mobile -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-3 flex justify-between items-center">
            <h1 class="text-xl sm:text-2xl font-bold text-amber-800">Coffee Art Shop</h1>
            <div class="flex items-center gap-2 sm:gap-4">
                @auth
                    <div class="flex items-center gap-1 sm:gap-2">
                        <!-- Wallet balance - Show icon only on smallest screens -->
                        <span
                            class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-amber-50 text-amber-800 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-3 w-3 sm:h-4 sm:w-4 sm:mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            <span class="text-xs sm:text-sm">${{ number_format(auth()->user()->balanceFloat, 2) }}</span>
                        </span>

                        <!-- Loyalty points - Show icon only on smallest screens -->
                        <span
                            class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-green-50 text-green-800 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-3 w-3 sm:h-4 sm:w-4 sm:mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                            <span class="text-xs sm:text-sm">{{ auth()->user()->loyalty_points ?? 0 }}</span>
                        </span>
                    </div>
                @endauth

                <!-- Cart button for desktop only -->
                <button
                    class="relative hidden sm:flex items-center px-2 py-1.5 sm:px-4 sm:py-2 bg-amber-600 text-white font-medium rounded-lg text-sm sm:text-base"
                    x-data="{ cartCount: 0 }" x-init="$wire.$on('cart-updated', () => { cartCount = $wire.cart.length })" @click="$dispatch('toggle-cart')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Cart
                    <span x-show="cartCount > 0" x-text="cartCount"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center text-xs"></span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-4 sm:py-6 pb-24 md:pb-6">
        <!-- Main Content -->
        <div x-data="{
            showCart: false,
            showCustomization: false,
            showCheckout: false,
            showCategories: false,
            showMenus: false
        }" class="relative">

            <!-- Search and Products Layout -->
            <div x-show="!showCustomization && !showCart && !showCheckout" class="space-y-4 sm:space-y-6">
                <!-- Search Bar -->
                <div class="flex items-center bg-white rounded-lg shadow overflow-hidden">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search for items..."
                            class="block w-full pl-10 pr-3 py-3 border-0 focus:ring-0 focus:outline-none">
                    </div>
                    @if ($search || $categoryFilter || $menuFilter)
                        <button wire:click="resetFilters" class="p-3 text-gray-400 hover:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- Filter Buttons for Mobile -->
                <div class="grid grid-cols-2 gap-3 md:hidden">
                    <!-- Category Filter Button -->
                    <button @click="showCategories = !showCategories; showMenus = false"
                        class="flex items-center justify-between px-4 py-2 bg-white rounded-lg shadow text-amber-800 font-medium">
                        <span>{{ $categoryFilter ? ucfirst($categoryFilter) : 'Categories' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform"
                            :class="{ 'rotate-180': showCategories }" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Menu Filter Button -->
                    <button @click="showMenus = !showMenus; showCategories = false"
                        class="flex items-center justify-between px-4 py-2 bg-white rounded-lg shadow text-amber-800 font-medium">
                        <span>{{ $menuFilter ? $menus->firstWhere('id', $menuFilter)?->title ?? 'Menus' : 'Menus' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform"
                            :class="{ 'rotate-180': showMenus }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Two Column Layout: Filters + Products -->
                <div class="flex flex-col md:flex-row gap-4 sm:gap-6">
                    <!-- Left Column - Filters (Mobile Drawer, Desktop Sidebar) -->
                    <div class="md:w-1/4 space-y-4">
                        <!-- Categories Filter -->
                        <div class="md:static fixed inset-0 z-30 md:z-auto transition-all duration-300 transform md:transform-none bg-white md:bg-transparent shadow-lg md:shadow-none overflow-auto md:overflow-visible"
                            :class="showCategories ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
                            <div class="flex md:hidden justify-between items-center p-4 border-b">
                                <h2 class="text-lg font-bold text-amber-800">Categories</h2>
                                <button @click="showCategories = false" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <h2 class="hidden md:block text-xl font-bold text-amber-800 mb-4">Categories</h2>
                                <div class="space-y-1">
                                    <button wire:click="setCategory(null)" @click="showCategories = false"
                                        class="w-full text-left px-3 py-2 rounded-md transition {{ $categoryFilter === null ? 'bg-amber-50 text-amber-800 font-medium' : 'hover:bg-gray-50' }}">
                                        All Items
                                    </button>

                                    @forelse ($this->categories() as $category)
                                        <button wire:click="setCategory('{{ $category->value }}')"
                                            @click="showCategories = false"
                                            class="w-full text-left px-3 py-2 rounded-md transition {{ $categoryFilter === $category->value ? 'bg-amber-50 text-amber-800 font-medium' : 'hover:bg-gray-50' }}">
                                            {{ ucfirst($category->name) }}
                                        </button>
                                    @empty
                                        <div class="text-gray-500 text-sm italic px-3 py-2">No categories available
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Menus Filter -->
                        <div class="md:static fixed inset-0 z-30 md:z-auto transition-all duration-300 transform md:transform-none bg-white md:bg-transparent shadow-lg md:shadow-none overflow-auto md:overflow-visible"
                            :class="showMenus ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
                            <div class="flex md:hidden justify-between items-center p-4 border-b">
                                <h2 class="text-lg font-bold text-amber-800">Menus</h2>
                                <button @click="showMenus = false" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <h2 class="hidden md:block text-xl font-bold text-amber-800 mb-4">Menus</h2>
                                <div class="space-y-1">
                                    <button wire:click="setMenu(null)" @click="showMenus = false"
                                        class="w-full text-left px-3 py-2 rounded-md transition {{ $menuFilter === null ? 'bg-amber-50 text-amber-800 font-medium' : 'hover:bg-gray-50' }}">
                                        All Menus
                                    </button>

                                    @forelse ($this->menus() as $menu)
                                        <button wire:click="setMenu({{ $menu->id }})" @click="showMenus = false"
                                            class="w-full text-left px-3 py-2 rounded-md transition {{ $menuFilter === $menu->id ? 'bg-amber-50 text-amber-800 font-medium' : 'hover:bg-gray-50' }}">
                                            {{ $menu->title }}
                                        </button>
                                    @empty
                                        <div class="text-gray-500 text-sm italic px-3 py-2">No menus available</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backdrop for mobile filter drawers -->
                    <div x-show="showCategories || showMenus" @click="showCategories = false; showMenus = false"
                        class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"></div>

                    <!-- Right Column - Products -->
                    <div class="md:w-3/4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            @forelse ($this->products() as $product)
                                <div
                                    class="bg-white rounded-lg shadow overflow-hidden {{ $this->isProductInCart($product->id) ? 'ring-2 ring-green-500' : '' }}">
                                    <div class="bg-amber-50 p-3 flex justify-center items-center h-36 sm:h-48">
                                        @if ($product->getFirstMediaUrl('product_images'))
                                            <img src="{{ $product->getFirstMediaUrl('product_images') }}" alt="{{ $product->name }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-amber-300"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="p-3 sm:p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                                                    {{ $product->name }}</h3>
                                                <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 mt-1">
                                                    {{ $product->description }}</p>
                                            </div>
                                            <span
                                                class="px-2 py-1 bg-amber-50 text-amber-700 font-semibold rounded-lg text-xs sm:text-sm whitespace-nowrap">
                                                ${{ number_format($product->base_price, 2) }}
                                            </span>
                                        </div>

                                        <div class="mt-3 sm:mt-4 flex gap-2">
                                            <button wire:click="startCustomizing({{ $product->id }})"
                                                @click="showCustomization = true"
                                                class="flex-1 px-2 py-1.5 sm:px-4 sm:py-2 bg-amber-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-amber-700">
                                                Customize
                                            </button>

                                            @if ($this->isProductInCart($product->id))
                                                <button wire:click="addProductToCart({{ $product->id }})"
                                                    class="flex-1 px-2 py-1.5 sm:px-4 sm:py-2 border border-green-600 bg-green-50 text-green-600 text-xs sm:text-sm font-medium rounded-lg hover:bg-green-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    In Cart ({{ $this->getProductQuantityInCart($product->id) }})
                                                </button>
                                            @else
                                                <button wire:click="addProductToCart({{ $product->id }})"
                                                    class="flex-1 px-2 py-1.5 sm:px-4 sm:py-2 border border-amber-600 text-amber-600 text-xs sm:text-sm font-medium rounded-lg hover:bg-amber-50">
                                                    <span wire:loading.remove
                                                        wire:target="addProductToCart({{ $product->id }})">
                                                        Add to Cart
                                                    </span>
                                                    <span wire:loading
                                                        wire:target="addProductToCart({{ $product->id }})">
                                                        Adding...
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="col-span-full flex flex-col items-center justify-center py-8 sm:py-12 bg-white rounded-lg shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-10 w-10 sm:h-12 sm:w-12 text-amber-300 mb-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 text-center text-sm sm:text-base">No products found.</p>
                                    @if ($search || $categoryFilter || $menuFilter)
                                        <button wire:click="resetFilters"
                                            class="mt-3 px-4 py-2 text-amber-700 text-sm sm:text-base underline hover:text-amber-800">
                                            Clear filters
                                        </button>
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customization Panel - Full screen on mobile -->
            <div x-show="showCustomization" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4"
                class="fixed inset-0 z-40 bg-white sm:static sm:z-auto sm:rounded-lg sm:shadow-lg p-4 sm:p-6 overflow-auto">
                @if ($currentProduct)
                    <div>
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Customize Your {{ $currentProduct->name }}
                            </h2>
                            <button wire:click="resetCustomization" @click="showCustomization = false"
                                class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <!-- Dynamic customization options - Fetch from database -->
                            @php
                                $options = App\Models\ProductOption::where('product_id', $currentProduct->id)
                                    ->where('is_available', true)
                                    ->orderBy('option_category')
                                    // ->orderBy('display_order')
                                    ->get()
                                    ->groupBy('option_category');
                            @endphp

                            @forelse ($options as $category => $categoryOptions)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">{{ ucfirst($category) }}</h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach ($categoryOptions as $option)
                                            <label
                                                class="flex items-center space-x-2 p-2 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                                :class="{
                                                    'bg-amber-50 border-amber-500': $wire.customizations[
                                                            {{ $currentProduct->id }}] && $wire.customizations[
                                                            {{ $currentProduct->id }}]['{{ $category }}'] ==
                                                        {{ $option->id }}
                                                }">
                                                <input type="radio" name="option_{{ $category }}"
                                                    value="{{ $option->id }}"
                                                    wire:click="setCustomization({{ $currentProduct->id }}, '{{ $category }}', {{ $option->id }})"
                                                    class="text-amber-600 focus:ring-amber-500"
                                                    :checked="$wire.customizations[{{ $currentProduct->id }}] && $wire
                                                        .customizations[{{ $currentProduct->id }}][
                                                            '{{ $category }}'
                                                        ] == {{ $option->id }}">
                                                <div>
                                                    <span class="font-medium">{{ $option->option_name }}</span>
                                                    @if ($option->additional_price > 0)
                                                        <span
                                                            class="text-sm text-amber-600 ml-1">+${{ number_format($option->additional_price, 2) }}</span>
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
                                <textarea wire:model="specialInstructions"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                                    rows="2" placeholder="Any special requests? (e.g., extra hot, less ice)"></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button wire:click="addToCart" @click="showCustomization = false"
                                    class="px-6 py-3 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Shopping Cart - Full screen on mobile -->
            <div x-show="showCart" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4" @toggle-cart.window="showCart = !showCart"
                class="fixed inset-0 z-40 bg-white sm:static sm:z-auto sm:rounded-lg sm:shadow-lg p-4 sm:p-6 overflow-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Your Cart</h2>
                    <button @click="showCart = false" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if (count($this->cart()) > 0)
                    <div class="space-y-4 mb-6">
                        @foreach ($this->cart() as $index => $item)
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
                                    <button wire:click="updateQuantity({{ $index }}, -1)"
                                        class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="font-medium">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $index }}, 1)"
                                        class="text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-right">
                                    <div class="font-medium">${{ number_format($item['total_price'], 2) }}</div>
                                    <button wire:click="removeFromCart({{ $index }})"
                                        class="text-xs text-red-600 hover:text-red-800">
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
                            <button @click="showCart = false; showCheckout = true"
                                class="w-full px-6 py-3 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700 cursor-pointer">
                                Proceed to Checkout
                            </button>
                            <button @click="showCart = false"
                                class="w-full px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg transition-all hover:bg-gray-50 cursor-pointer">
                                Continue Shopping
                            </button>
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-gray-500 mb-4">Your cart is empty</p>
                        <button @click="showCart = false"
                            class="px-6 py-2 bg-amber-600 text-white font-medium rounded-lg transition-all hover:bg-amber-700 cursor-pointer ">
                            Browse Products
                        </button>
                    </div>
                @endif
            </div>

            <!-- Checkout Form - Full screen on mobile -->
            <div x-show="showCheckout" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform translate-y-4"
                class="fixed inset-0 z-40 bg-white sm:static sm:z-auto sm:rounded-lg sm:shadow-lg p-4 sm:p-6 overflow-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Finalize Your Order</h2>
                    <button @click="showCheckout = false; showCart = true" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Price (incl. tax):</span>
                                <span>${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 border-b border-gray-200 pb-2">
                                <span>Tax ({{ number_format($taxRate * 100) }}% included):</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>

                            @if ($usePoints && $hasEnoughLoyaltyPoints)
                                <div class="flex justify-between text-green-700 mt-2">
                                    <span>Loyalty Points Discount:</span>
                                    <span>-${{ number_format($cartTotal, 2) }}</span>
                                </div>
                            @elseif ($pointsToRedeem > 0)
                                <div class="flex justify-between text-green-700 mt-2">
                                    <span>Points Discount:</span>
                                    <span>-${{ number_format($pointsToRedeem * 0.1, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between font-medium text-lg border-t border-gray-300 pt-2 mt-2">
                                <span>Total:</span>
                                <span>${{ number_format($totalAmount, 2) }}</span>
                            </div>

                            @auth
                                <div class="mt-3 text-sm text-gray-700">
                                    @if (!$usePoints)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-4 h-4 mr-1 text-green-600">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span>You'll earn {{ (int) floor($totalAmount * 10) }} loyalty points with this
                                                purchase!</span>
                                        </div>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Payment Method</h3>
                        <div class="space-y-3">
                            <!-- Wallet Payment Option -->
                            @auth
                                <label
                                    class="flex items-center justify-between p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                    :class="{ 'bg-amber-50 border-amber-500': $wire.paymentMethod === 'wallet' }">
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" name="paymentMethod" value="wallet"
                                            wire:model.live="paymentMethod" class="text-amber-600 focus:ring-amber-500">
                                        <div>
                                            <span class="font-medium">Wallet Balance</span>
                                            <p class="text-sm text-gray-500">Pay using your account balance</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-medium">${{ number_format($walletBalance, 2) }}</span>
                                        @if (!$hasEnoughBalance && $paymentMethod === 'wallet')
                                            <p class="text-xs text-red-600">Insufficient balance</p>
                                        @endif
                                    </div>
                                </label>

                                @if (!$hasEnoughBalance && $paymentMethod === 'wallet')
                                    <div class="bg-red-50 border border-red-100 text-red-700 p-3 rounded-md text-sm">
                                        <div class="flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Your balance of ${{ number_format($walletBalance, 2) }} is less than the
                                                total amount of ${{ number_format($totalAmount, 2) }}. Please select
                                                another payment method or <a href="{{ route('gift-cards.redeem') }}"
                                                    class="underline font-medium">add funds to your wallet</a>.</span>
                                        </div>
                                    </div>
                                @endif
                            @endauth

                            <!-- Cash Option -->
                            <label
                                class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                :class="{ 'bg-amber-50 border-amber-500': $wire.paymentMethod === 'cash' }">
                                <input type="radio" name="paymentMethod" value="cash"
                                    wire:model.live="paymentMethod" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="font-medium">Cash</span>
                                    <p class="text-sm text-gray-500">Pay at pickup</p>
                                </div>
                            </label>

                            <!-- Credit Card Option -->
                            {{-- <label
                                class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                :class="{ 'bg-amber-50 border-amber-500': $wire.paymentMethod === 'card' }">
                                <input type="radio" name="paymentMethod" value="card"
                                    wire:model.live="paymentMethod" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="font-medium">Credit Card</span>
                                    <p class="text-sm text-gray-500">Pay at pickup with card</p>
                                </div>
                            </label> --}}

                            <!-- Loyalty Points Payment Option -->
                            @auth
                                <label
                                    class="flex items-center justify-between p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                    :class="{ 'bg-green-50 border-green-500': $wire.usePoints }">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" wire:model.live="usePoints"
                                            class="text-green-600 focus:ring-green-500 rounded">
                                        <div>
                                            <span class="font-medium">Pay with Loyalty Points</span>
                                            <p class="text-sm text-gray-500">Use your loyalty points to pay for this order
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-medium">{{ $availablePoints }} points</span>
                                        <p
                                            class="text-xs {{ $hasEnoughLoyaltyPoints ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $hasEnoughLoyaltyPoints ? 'Enough points' : 'Not enough points' }}
                                        </p>
                                    </div>
                                </label>

                                @if ($usePoints && !$hasEnoughLoyaltyPoints)
                                    <div class="bg-red-50 border border-red-100 text-red-700 p-3 rounded-md text-sm">
                                        <div class="flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>You need {{ $requiredPoints }} points for this purchase, but you only
                                                have {{ $availablePoints }} points.</span>
                                        </div>
                                    </div>
                                @endif

                                @if ($usePoints && $hasEnoughLoyaltyPoints)
                                    <div class="bg-green-50 border border-green-100 text-green-700 p-3 rounded-md text-sm">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>This order will use {{ $requiredPoints }} points, saving you
                                                {{ '$' . number_format($cartTotal, 2) }}!</span>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Redemption Type -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Pickup Option</h3>
                        <div class="flex space-x-4">
                            <label
                                class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                :class="{ 'bg-amber-50 border-amber-500': $wire.redemptionType === 'in-store' }">
                                <input type="radio" name="redemptionType" value="in-store"
                                    wire:model="redemptionType" class="text-amber-600 focus:ring-amber-500">
                                <span class="font-medium">In-Store Pickup</span>
                            </label>
                            <label
                                class="flex items-center space-x-2 p-3 border rounded-md cursor-pointer hover:bg-gray-100 transition-colors"
                                :class="{ 'bg-amber-50 border-amber-500': $wire.redemptionType === 'online' }">
                                <input type="radio" name="redemptionType" value="online"
                                    wire:model="redemptionType" class="text-amber-600 focus:ring-amber-500">
                                <span class="font-medium">Order Online</span>
                            </label>
                        </div>
                    </div>

                    <!-- Points Redemption -->
                    @if (auth()->check() && auth()->user()->loyalty_points > 0)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Redeem Points</h3>
                            <p class="text-gray-600 mb-2">You have {{ auth()->user()->loyalty_points }} points
                                available.</p>
                            <div class="flex items-center space-x-4">
                                <input type="number" min="0" max="{{ auth()->user()->loyalty_points }}"
                                    wire:model="pointsToRedeem"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50 w-24">
                                <span class="text-gray-600">points = ${{ number_format($pointsToRedeem * 0.1, 2) }}
                                    discount</span>
                            </div>
                        </div>
                    @endif

                    <!-- Personal Message -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Personal Message</h3>
                        <textarea wire:model="personalMessage"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50"
                            rows="3" placeholder="Add a personal message for your order (optional)"></textarea>
                    </div>

                    <div class="flex justify-center sm:justify-end ">
                        <button wire:click="placeOrder"
                            class="w-full mb-12 sm:w-auto px-6 py-3 bg-amber-600 text-white font-medium rounded-lg text-base transition-all hover:bg-amber-700 disabled:opacity-70 disabled:cursor-not-allowed"
                              wire:loading.attr="disabled" @if ($paymentMethod === 'wallet' && !$hasEnoughBalance) disabled @endif>
                            <span wire:loading.remove>Place Order</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>

                    @if (!auth()->check())
                        <div class="border-t pt-4 mt-2 text-center">
                            <p class="text-sm text-gray-600">
                                <a href="{{ route('login') }}" class="text-amber-600 font-medium">Sign in</a> to use
                                your wallet balance or earn points with your purchase.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Fixed Cart Button for Mobile (hidden during checkout and cart view) -->
    <div class="fixed bottom-0 left-0 right-0 sm:hidden z-50"
         x-data="{ cartCount: 0 }"
         x-init="$wire.$on('cart-updated', () => { cartCount = $wire.cart.length })"
         x-show="!showCheckout">
        <div class="flex items-center justify-between bg-white shadow-lg border-t border-gray-200 p-3">
            <div class="flex flex-col">
                <span class="text-gray-500 text-xs">Your Cart</span>
                <span class="font-semibold text-gray-900">${{ number_format($cartTotal, 2) }}</span>
            </div>
            <button
                @click="$dispatch('toggle-cart')"
                class="relative flex items-center justify-center gap-2 px-4 py-2 bg-amber-600 text-white font-medium rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>View Cart</span>
                <span x-show="cartCount > 0" x-text="cartCount"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs"></span>
            </button>
        </div>
    </div>
</div>
