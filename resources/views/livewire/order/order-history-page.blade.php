<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Order History</h1>
            <p class="mt-2 text-sm text-gray-600">View all your past orders</p>
        </div>

        @if(!auth()->check())
            <!-- Not logged in state -->
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h2 class="mt-4 text-lg font-medium text-gray-900">Sign in to view your orders</h2>
                <p class="mt-2 text-sm text-gray-600">You need to be logged in to view your order history</p>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-medium text-white hover:bg-amber-700">
                        Sign In
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Filters -->
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-medium text-gray-900">Filter Orders</h3>
                        </div>
                        <div class="flex flex-wrap items-center space-x-2">
                            <div class="flex space-x-2">
                                <button
                                    wire:click="filterByStatus(null)"
                                    class="px-3 py-1.5 text-sm font-medium rounded-md {{ $status === '' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                                >
                                    All
                                </button>
                                <button
                                    wire:click="filterByStatus('completed')"
                                    class="px-3 py-1.5 text-sm font-medium rounded-md {{ $status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                                >
                                    Completed
                                </button>
                                <button
                                    wire:click="filterByStatus('cancelled')"
                                    class="px-3 py-1.5 text-sm font-medium rounded-md {{ $status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}"
                                >
                                    Cancelled
                                </button>
                            </div>

                            <div>
                                <button
                                    wire:click="resetFilters"
                                    class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900"
                                >
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('id')" class="group inline-flex items-center">
                                        Order #
                                        @if ($sortField === 'id')
                                            <svg class="ml-1 h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('created_at')" class="group inline-flex items-center">
                                        Date
                                        @if ($sortField === 'created_at')
                                            <svg class="ml-1 h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Items
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('status')" class="group inline-flex items-center">
                                        Status
                                        @if ($sortField === 'status')
                                            <svg class="ml-1 h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('total_amount')" class="group inline-flex items-center">
                                        Total
                                        @if ($sortField === 'total_amount')
                                            <svg class="ml-1 h-4 w-4 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @else
                                            <svg class="ml-1 h-4 w-4 opacity-0 group-hover:opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->created_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">
                                            @foreach($order->items->take(2) as $item)
                                                {{ $item->product->name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <span>+ {{ $order->items->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($order->status->value)
                                                @case('pending') bg-blue-100 text-blue-800 @break
                                                @case('processing') bg-yellow-100 text-yellow-800 @break
                                                @case('ready') bg-green-100 text-green-800 @break
                                                @case('completed') bg-gray-100 text-gray-800 @break
                                                @case('cancelled') bg-red-100 text-red-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ ucfirst($order->status->value) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            ${{ number_format($order->total_amount, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-500 capitalize">
                                            {{ $order->payment_method }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('orders.track', $order->id) }}" class="text-amber-600 hover:text-amber-900">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <h3 class="mt-4 text-lg font-medium text-gray-900">No orders found</h3>
                                            <p class="mt-2 text-sm text-gray-600">You haven't placed any orders yet</p>
                                            <div class="mt-6">
                                                <a href="{{ route('order') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-medium text-white hover:bg-amber-700">
                                                    Place an Order
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $orders->links() }}
                </div>
            </div>

            <!-- Back to tracking -->
            <div class="mt-6 text-center">
                <a href="{{ route('orders.track') }}" class="inline-flex items-center text-sm font-medium text-amber-600 hover:text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Order Tracking
                </a>
            </div>
        @endif
    </div>
</div>
