<x-filament-widgets::widget>
    <div wire:poll.10s="$dispatch('refresh-orders')">
        <x-filament::section>
            <div>
                {{-- Filters Bar --}}
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Status:</span>
                        <select wire:model.live="statusFilter"
                            class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="all">All Active</option>
                            @foreach (\App\Enum\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model.live="todayOnly"
                            class="rounded text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                        <span class="text-sm font-medium">Today's orders</span>
                    </label>

                    <button wire:click="toggleSortDirection" type="button"
                        class="inline-flex items-center px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                        <span class="mr-1">Sort:</span>
                        @if ($sortDirection === 'asc')
                            <span class="flex items-center">
                                Oldest First
                                <x-heroicon-s-arrow-up class="ml-1 w-4 h-4" />
                            </span>
                        @else
                            <span class="flex items-center">
                                Newest First
                                <x-heroicon-s-arrow-down class="ml-1 w-4 h-4" />
                            </span>
                        @endif
                    </button>

                    <div class="ml-auto">
                        <button type="button" wire:click="loadOrders"
                            class="inline-flex items-center justify-center font-medium text-sm text-primary-600 hover:underline focus:outline-none focus:underline">
                            <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                            Refresh
                        </button>
                    </div>
                </div>

                {{-- Last Refresh Indicator --}}
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Auto-refreshes every 10 seconds. Last updated: {{ now()->format('H:i:s') }}
                </p>


                {{-- Display remaining orders in a grid --}}
                @if ($orders && $orders->count() > 1)
                    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($orders as $order)
                            <div
                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 h-full flex flex-col">
                                {{-- Order Header --}}
                                <div
                                    class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                                    <div class="font-medium">
                                        Order #{{ $order->order_number ?? $order->id }}
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-yellow-100 text-yellow-800' =>
                                                $order->status->value === \App\Enum\OrderStatus::PENDING->value,
                                            'bg-blue-100 text-blue-800' =>
                                                $order->status->value === \App\Enum\OrderStatus::PROCESSING->value,
                                            'bg-indigo-100 text-indigo-800' =>
                                                $order->status->value === \App\Enum\OrderStatus::READY->value,
                                            'bg-green-100 text-green-800' =>
                                                $order->status->value === \App\Enum\OrderStatus::COMPLETED->value,
                                            'bg-red-100 text-red-800' =>
                                                $order->status->value === \App\Enum\OrderStatus::CANCELLED->value,
                                        ])>
                                            {{ $order->status->value }}
                                        </span>

                                        <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-amber-100 text-amber-800' =>
                                                $order->payment_status === \App\Enum\PaymentStatus::PENDING->value,
                                            'bg-green-100 text-green-800' =>
                                                $order->payment_status === \App\Enum\PaymentStatus::PAID->value,
                                            'bg-red-100 text-red-800' =>
                                                $order->payment_status === \App\Enum\PaymentStatus::FAILED->value,
                                        ])>
                                            {{ $order->payment_status }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Order Content --}}
                                <div class="px-4 py-3 space-y-3 flex-grow">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Customer</span>
                                            <div>{{ $order->user ? $order->user->name : 'Guest' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Amount</span>
                                            <div class="text-right">${{ number_format($order->total_amount, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Payment Method --}}
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Payment
                                                Method</span>
                                            <div class="capitalize">{{ $order->payment_method ?? 'Not specified' }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Created</span>
                                            <div>{{ $order->created_at->format('H:i:s') }}</div>
                                        </div>
                                    </div>

                                    {{-- Order Items --}}
                                    <div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Order Items</span>
                                        <div class="mt-1 max-h-40 overflow-y-auto">
                                            @forelse($order->items as $item)
                                                <div
                                                    class="py-2 {{ !$loop->last ? 'border-b dark:border-gray-700' : '' }}">
                                                    <div class="flex justify-between text-sm">
                                                        <span>{{ $item->quantity }}x
                                                            {{ $item->product->name ?? 'Unknown Product' }}</span>
                                                        <span>${{ number_format($item->total_price, 2) }}</span>
                                                    </div>

                                                    @if ($item->orderItemCustomizations && $item->orderItemCustomizations->isNotEmpty())
                                                        <div class="pl-3 mt-1">
                                                            @foreach ($item->orderItemCustomizations as $customization)
                                                                <div
                                                                    class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                                                    @if ($customization->productOption && $customization->productOption->option_category)
                                                                        <span>{{ $customization->productOption->option_category }}:
                                                                            {{ $customization->productOption->option_name }}</span>
                                                                    @else
                                                                        <span>Custom option</span>
                                                                    @endif

                                                                    @if ($customization->option_price > 0)
                                                                        <span>+${{ number_format($customization->option_price, 2) }}</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($item->special_instructions)
                                                        <div
                                                            class="mt-1 pl-3 text-xs italic text-gray-500 dark:text-gray-400">
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
                                </div>

                                {{-- Order Actions --}}
                                <div
                                    class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 mt-auto">
                                    <div class="flex flex-wrap gap-2">

                                        @if ($order->status->value === \App\Enum\OrderStatus::READY->value)
                                            <x-filament::button color="primary" size="sm"
                                                wire:click="markAsConfirm({{ $order->id }})"
                                                wire:loading.attr="disabled" icon="heroicon-s-clipboard-document-check">
                                                Confirm
                                            </x-filament::button>
                                        @elseif($order->status->value === \App\Enum\OrderStatus::PENDING->value || \App\Enum\OrderStatus::PROCESSING->value)
                                            <x-filament::button color="info" size="sm"
                                                wire:click="markAsReady({{ $order->id }})"
                                                wire:loading.attr="disabled" icon="heroicon-s-shopping-bag">
                                                Ready To Pick Up
                                            </x-filament::button>
                                        @endif
                                        <x-filament::button color="info" size="sm"
                                        wire:click="markAsProgrees({{ $order->id }})"
                                        wire:loading.attr="disabled" icon="heroicon-s-shopping-bag">
                                            Progreess
                                    </x-filament::button>

                                        <x-filament::button color="danger" size="sm"
                                            wire:click="cancelOrder({{ $order->id }})" wire:loading.attr="disabled"
                                            icon="heroicon-s-x-mark">
                                            Cancle
                                        </x-filament::button>


                                        {{-- Mark Paid for Cash Orders --}}
                                        @if ($order->payment_method === 'cash' && $order->payment_status === \App\Enum\PaymentStatus::PENDING->value)
                                            <x-filament::button color="success" size="sm"
                                                wire:click="markAsPaid({{ $order->id }})"
                                                wire:loading.attr="disabled" icon="heroicon-s-banknotes">
                                                Mark Paid
                                            </x-filament::button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <x-heroicon-o-shopping-bag class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            No orders match your current filters.
                        </p>
                        <div class="mt-6">
                            <button wire:click="loadOrders" type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <x-heroicon-o-arrow-path class="-ml-1 mr-2 h-5 w-5" />
                                Refresh Orders
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
