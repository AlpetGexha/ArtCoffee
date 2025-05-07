<div class="space-y-2">
    <x-filament::dropdown>
        <x-slot name="trigger">
            <button
                type="button"
                class="inline-flex items-center justify-center gap-1 px-3 py-1 text-sm font-medium rounded-lg text-primary-600 hover:bg-primary-500/10 focus:outline-none focus:ring-2 focus:ring-primary-600"
            >
                <span>{{ $getRecord()?->orderItems?->count() }} {{ \Illuminate\Support\Str::plural('item', $getRecord()->orderItems->count()) }}</span>
                <x-heroicon-s-chevron-down class="w-4 h-4" />
            </button>
        </x-slot>

        <x-filament::dropdown.list class="w-80 max-h-80 overflow-y-auto">
            <div class="p-2">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Order #{{ $getRecord()->order_number }}
                </h4>

                <div class="border-b dark:border-gray-700 mb-3"></div>

                @forelse ($getRecord()->orderItems->sortByDesc('created_at') as $orderItem)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-b dark:border-gray-700' : '' }}">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $orderItem->quantity }}x {{ $orderItem->product->name }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">
                                ${{ number_format($orderItem->total_price, 2) }}
                            </span>
                        </div>

                        @if ($orderItem->orderItemCustomizations->isNotEmpty())
                            <div class="mt-2 pl-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Customizations:</p>
                                <ul class="space-y-1">
                                    @foreach ($orderItem->orderItemCustomizations as $customization)
                                        <li class="text-xs flex justify-between">
                                            <span class="text-gray-600 dark:text-gray-300">
                                                {{ $customization->productOption->optionCategory->name }}: {{ $customization->productOption->name }}
                                            </span>
                                            @if ($customization->option_price > 0)
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    +${{ number_format($customization->option_price, 2) }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($orderItem->special_instructions)
                            <div class="mt-2 pl-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Instructions:</p>
                                <p class="text-xs text-gray-600 dark:text-gray-300 italic">
                                    "{{ $orderItem->special_instructions }}"
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No items in this order.</p>
                @endforelse
            </div>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
