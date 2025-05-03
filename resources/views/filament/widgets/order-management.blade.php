<x-filament-widgets::widget>
    <x-filament::section>
        <div>
            {{-- Filters --}}
            <div class="flex flex-wrap items-center gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium">Status:</span>
                    <select
                        wire:model.live="statusFilter"
                        class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="all">All Active</option>
                        @foreach(\App\Enum\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" wire:model.live="todayOnly" class="rounded text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                    <span class="text-sm font-medium">Today's orders</span>
                </label>

                <div class="ml-auto">
                    <button
                        type="button"
                        wire:click="loadOrders"
                        class="inline-flex items-center justify-center font-medium text-sm text-primary-600 hover:underline focus:outline-none focus:underline"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                        Refresh
                    </button>
                </div>
            </div>

            {{-- Orders Grid --}}
            @if($orders && $orders->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($orders as $order)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                            {{-- Order Header --}}
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                <div class="font-medium">
                                    Order #{{ $order->order_number }}
                                </div>
                                <div>
                                    <span @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-yellow-100 text-yellow-800' => $order->status === \App\Enum\OrderStatus::PENDING->value,
                                        'bg-blue-100 text-blue-800' => $order->status === \App\Enum\OrderStatus::PROCESSING->value,
                                        'bg-green-100 text-green-800' => $order->status === \App\Enum\OrderStatus::COMPLETED->value,
                                        'bg-red-100 text-red-800' => $order->status === \App\Enum\OrderStatus::CANCELLED->value,
                                    ])>
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>

                            {{-- Order Content --}}
                            <div class="px-4 py-3 space-y-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Customer</span>
                                        <div>{{ $order->user ? $order->user->name : 'Guest' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Amount</span>
                                        <div class="text-right">${{ number_format($order->total_amount, 2) }}</div>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Order Items</span>
                                    <div class="mt-1 max-h-40 overflow-y-auto">
                                        @forelse($order->items as $item)
                                            <div class="py-2 {{ !$loop->last ? 'border-b dark:border-gray-700' : '' }}">
                                                <div class="flex justify-between text-sm">
                                                    <span>{{ $item->quantity }}x {{ $item->product->name ?? 'Unknown Product' }}</span>
                                                    <span>${{ number_format($item->total_price, 2) }}</span>
                                                </div>

                                                @if($item->orderItemCustomizations && $item->orderItemCustomizations->isNotEmpty())
                                                    <div class="pl-3 mt-1">
                                                        @foreach($item->orderItemCustomizations as $customization)
                                                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                                                @if($customization->productOption && $customization->productOption->optionCategory)
                                                                    <span>{{ $customization->productOption->optionCategory->name }}: {{ $customization->productOption->name }}</span>
                                                                @else
                                                                    <span>Custom option</span>
                                                                @endif

                                                                @if($customization->option_price > 0)
                                                                    <span>+${{ number_format($customization->option_price, 2) }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($item->special_instructions)
                                                    <div class="mt-1 pl-3 text-xs italic text-gray-500 dark:text-gray-400">
                                                        "{{ $item->special_instructions }}"
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-sm text-gray-500 dark:text-gray-400 py-2">
                                                No items found
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                    <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>

                            {{-- Order Actions --}}
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex justify-between">
                                @if($order->status === \App\Enum\OrderStatus::PENDING->value)
                                    <button
                                        wire:click="markAsProcessing({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-1" />
                                        Processing
                                    </button>
                                @elseif($order->status === \App\Enum\OrderStatus::PROCESSING->value)
                                    <button
                                        wire:click="markAsPicked({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                                        Mark Picked
                                    </button>
                                @else
                                    <span></span>
                                @endif

                                <a
                                    href="{{ route('filament.admin.resources.orders.edit', $order) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                >
                                    <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <x-heroicon-o-shopping-bag class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        No orders match your current filters.
                    </p>
                    <div class="mt-6">
                        <button
                            wire:click="loadOrders"
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            <x-heroicon-o-arrow-path class="-ml-1 mr-2 h-5 w-5" />
                            Refresh Orders
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
