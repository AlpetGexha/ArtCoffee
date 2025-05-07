<div class="min-h-screen bg-gray-50 py-8"
    x-data="{
        init() {
            // Listen for Echo events through Alpine.js
            Echo.channel('orders')
                .listen('.order.updated', (event) => {
                    console.log('Check');

                    if (event.id == @this.get('orderId')) {
                        console.log('refresh before');
                        @this.refresh();
                        $dispatch('refresh-notifications');
                        console.log('refresh after');
                    }
                });

                Echo.private(`orders.`+@this.get('orderId'))
                    .listen('.order.updated', (event) => {
                        console.log('JASHT IF 2');
                        @this.refresh();
                        console.log('mrena IF 2');
                    });
        }
    }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Order Tracking</h1>
            <p class="mt-2 text-sm text-gray-600">Track the status of your orders in real-time</p>
        </div>

        <!-- Authentication check -->
        @if (!auth()->check() && !$currentOrder)
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="mt-4 text-lg font-medium text-gray-900">Sign in to view your orders</h2>
                <p class="mt-2 text-sm text-gray-600">You'll need to sign in to track your orders</p>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-medium text-white hover:bg-amber-700">
                        Sign In
                    </a>
                </div>
            </div>
        @else
            <!-- Mobile tab navigation for switching between current order and all orders -->
            <div class="sm:hidden mb-6">
                <div class="flex space-x-2 bg-white rounded-lg shadow p-1">
                    <button
                        x-data
                        @click="$wire.orderId = null; $wire.loadCurrentOrder()"
                        class="flex-1 text-center py-2 px-4 text-sm font-medium rounded-md {{ !$orderId ? 'bg-amber-50 text-amber-800' : 'text-gray-700 hover:bg-gray-100' }}"
                    >
                        All Orders
                    </button>
                    @if ($currentOrder)
                        <button
                            class="flex-1 text-center py-2 px-4 text-sm font-medium rounded-md {{ $orderId ? 'bg-amber-50 text-amber-800' : 'text-gray-700 hover:bg-gray-100' }}"
                        >
                            Order #{{ $currentOrder->id }}
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- All in progress orders (sidebar on desktop, tab on mobile) -->
                <div class="md:col-span-1 {{ $currentOrder && !$orderId ? 'hidden md:block' : '' }}">
                    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                        <div class="px-4 py-5 sm:px-6 bg-amber-50 border-b border-amber-100">
                            <h2 class="text-lg font-medium text-amber-800">Active Orders</h2>
                            <p class="mt-1 max-w-2xl text-sm text-amber-600">
                                Your current in-progress orders
                            </p>
                        </div>

                        @forelse ($this->inProgressOrders as $order)
                            <a
                                href="{{ route('orders.track', $order->id) }}"
                                class="block border-b border-gray-200 hover:bg-gray-50 transition-colors {{ $order->id === $orderId ? 'bg-amber-50' : '' }}"
                            >
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Order #{{ $order->id }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->created_at->format('M j, g:i A') }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($order->status->value)
                                                @case('pending') bg-blue-100 text-blue-800 @break
                                                @case('processing') bg-yellow-100 text-yellow-800 @break
                                                @case('ready') bg-green-100 text-green-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ ucfirst($order->status->value) }}
                                        </span>
                                    </div>

                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No active orders</h3>
                                <p class="mt-1 text-sm text-gray-500">You don't have any orders in progress.</p>
                                <div class="mt-6">
                                    <a href="{{ route('order') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700">
                                        Place an Order
                                    </a>
                                </div>
                            </div>
                        @endforelse

                        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
                            <div class="flex justify-between">
                                <a href="{{ route('orders.history') }}" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                    View Order History
                                </a>
                                <button wire:click="refresh" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current order tracking (main content) -->
                @if ($currentOrder)
                    <div class="md:col-span-2">
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-amber-50 to-amber-100 border-b border-amber-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                            Order #{{ $currentOrder->id }}
                                        </h3>
                                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                            Placed on {{ $currentOrder->created_at->format('M j, Y') }} at {{ $currentOrder->created_at->format('g:i A') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @switch($currentOrder->status->value)
                                            @case('pending') bg-blue-100 text-blue-800 @break
                                            @case('processing') bg-yellow-100 text-yellow-800 @break
                                            @case('ready') bg-green-100 text-green-800 @break
                                            @case('completed') bg-green-100 text-green-800 @break
                                            @case('cancelled') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ ucfirst($currentOrder->status->value) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Order Progress Tracker -->
                            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                                <h4 class="text-base font-medium text-gray-900 mb-3">Order Progress</h4>

                                <!-- Progress Bar -->
                                <div class="w-full bg-gray-200 rounded-full h-3 mb-6">
                                    <div class="bg-amber-500 h-3 rounded-full transition-all duration-700 ease-in-out" style="width: {{ $this->getOrderProgressPercentage() }}%"></div>
                                </div>

                                <!-- Progress Steps -->
                                <div class="relative">
                                    <!-- Progress Timeline -->
                                    <div class="hidden sm:block w-full h-0.5 bg-gray-200 absolute top-5 left-0 z-0"></div>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                        <!-- Order Received -->
                                        <div class="relative flex flex-col items-center">
                                            <div class="rounded-full h-10 w-10 flex items-center justify-center z-10
                                                {{ in_array($currentOrder->status->value, ['pending', 'processing', 'ready', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div class="text-center mt-2">
                                                <h5 class="text-sm font-medium text-gray-900">Order Received</h5>
                                                <p class="text-xs text-gray-500 mt-1">{{ $currentOrder->created_at->format('g:i A') }}</p>
                                            </div>
                                        </div>

                                        <!-- In Preparation -->
                                        <div class="relative flex flex-col items-center">
                                            <div class="rounded-full h-10 w-10 flex items-center justify-center z-10
                                                {{ in_array($currentOrder->status->value, ['processing', 'ready', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                                @if (in_array($currentOrder->status->value, ['processing', 'ready', 'completed']))
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-center mt-2">
                                                <h5 class="text-sm font-medium text-gray-900">In Preparation</h5>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if ($currentOrder->status->value === 'pending')
                                                        Estimated
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Ready for Pickup -->
                                        <div class="relative flex flex-col items-center">
                                            <div class="rounded-full h-10 w-10 flex items-center justify-center z-10
                                                {{ in_array($currentOrder->status->value, ['ready', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                                @if (in_array($currentOrder->status->value, ['ready', 'completed']))
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-center mt-2">
                                                <h5 class="text-sm font-medium text-gray-900">Ready for Pickup</h5>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if ($currentOrder->status->value === 'ready')
                                                        Now
                                                    @elseif (!in_array($currentOrder->status->value, ['completed', 'cancelled']))
                                                        {{ $this->getEstimatedReadyTime() }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Completed -->
                                        <div class="relative flex flex-col items-center">
                                            <div class="rounded-full h-10 w-10 flex items-center justify-center z-10
                                                {{ $currentOrder->status->value === 'completed' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                                @if ($currentOrder->status->value === 'completed')
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-center mt-2">
                                                <h5 class="text-sm font-medium text-gray-900">Completed</h5>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @if ($currentOrder->status->value === 'completed')
                                                        {{ $currentOrder->completed_at?->format('g:i A') ?? 'Done' }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($currentOrder->status->value === 'ready')
                                    <div class="mt-8 flex justify-center">
                                        <button
                                            wire:click="confirmPickup"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700"
                                        >
                                            Confirm Pickup
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Order Details -->
                            <div class="px-4 py-5 sm:px-6">
                                <h4 class="text-base font-medium text-gray-900 mb-4">Order Details</h4>

                                <!-- Pickup Location -->
                                <div class="border border-gray-200 rounded-md p-4 mb-6 bg-gray-50">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Pickup Location</h5>
                                    <p class="text-sm text-gray-900">
                                        {{ $currentOrder->branch->name }}<br>
                                        {{ $currentOrder->branch->address }}
                                    </p>
                                </div>

                                <!-- Order Items -->
                                <div class="space-y-4">
                                    @foreach ($currentOrder->items as $item)
                                        <div class="border-b border-gray-200 pb-4">
                                            <div class="flex justify-between">
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h5>
                                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>

                                                    @if ($item->customizations->isNotEmpty())
                                                        <div class="mt-2">
                                                            <h6 class="text-xs font-medium text-gray-700">Customizations:</h6>
                                                            <ul class="mt-1 text-xs text-gray-500 space-y-1">
                                                                @foreach ($item->customizations as $customization)
                                                                    <li>• {{ $customization->productOption->name }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if ($item->special_instructions)
                                                        <div class="mt-2 text-xs italic text-gray-500">
                                                            "{{ $item->special_instructions }}"
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-sm font-medium text-gray-900">${{ number_format($item->total_price, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Summary -->
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal</span>
                                        <span class="text-gray-900">${{ number_format($currentOrder->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm mt-2">
                                        <span class="text-gray-600">Tax</span>
                                        <span class="text-gray-900">${{ number_format($currentOrder->tax, 2) }}</span>
                                    </div>
                                    @if ($currentOrder->discount > 0)
                                        <div class="flex justify-between text-sm mt-2">
                                            <span class="text-gray-600">Discount</span>
                                            <span class="text-green-600">-${{ number_format($currentOrder->discount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                                        <span class="text-base font-medium text-gray-900">Total</span>
                                        <span class="text-base font-medium text-gray-900">${{ number_format($currentOrder->total_amount, 2) }}</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        Paid via <span class="font-medium capitalize">{{ $currentOrder->payment_method }}</span>
                                    </div>
                                </div>

                                @if ($currentOrder->special_instructions)
                                    <div class="mt-6 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Special Instructions</h5>
                                        <p class="text-sm text-gray-600 italic">"{{ $currentOrder->special_instructions }}"</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer actions -->
                            <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between items-center">
                                <div class="flex items-center text-sm text-gray-500">
                                    <span>
                                        <svg class="inline-block h-3.5 w-3.5 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                        </svg>
                                        Real-time updates enabled
                                    </span>
                                </div>
                                <button
                                    wire:click="refresh"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                                >
                                    <svg class="h-3.5 w-3.5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Refresh Now
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif ($this->inProgressOrders->isEmpty())
                    <div class="md:col-span-2">
                        <div class="bg-white shadow rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No active orders</h3>
                            <p class="mt-2 text-sm text-gray-600">You don't have any orders in progress right now</p>
                            <div class="mt-6">
                                <a href="{{ route('order') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-medium text-white hover:bg-amber-700">
                                    Place an Order
                                </a>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('orders.history') }}" class="text-sm font-medium text-amber-600 hover:text-amber-500">
                                    View Order History
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
