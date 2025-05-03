<div
    x-data="{ open: @entangle('isOpen').defer }"
    @click.away="open = false"
    @keydown.escape.window="open = false"
    class="relative"
    wire:poll.2s="$dispatch('refresh-notifications')"
>
    <!-- Notification Bell Icon with Badge -->
    <button
        x-on:click.stop.prevent="open = !open"
        type="button"
        class="relative flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-400 focus:ring-offset-2 transition"
        aria-label="Notifications"
        id="notification-menu-button"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-zinc-700 dark:text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if ($notifications->isNotEmpty())
            <span class="absolute top-0 right-0 h-4 w-4 rounded-full bg-red-500 flex items-center justify-center text-xs text-white">
                {{ $notifications->count() > 9 ? '9+' : $notifications->count() }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-zinc-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        @click.outside="open = false"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="notification-menu-button"
        tabindex="-1"
    >
        <div class="p-2">
            <div class="flex items-center justify-between border-b dark:border-zinc-700 pb-2 mb-2">
                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Notifications</h3>

                @if ($notifications->isNotEmpty())
                    <button
                        wire:click.stop="markAllAsRead"
                        class="text-sm text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                        role="menuitem"
                        tabindex="-1"
                    >
                        Mark all as read
                    </button>
                @endif
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <div
                        class="py-2 px-1 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-md transition"
                        role="menuitem"
                        tabindex="-1"
                    >
                        <div class="flex items-start space-x-2">
                            <!-- Notification Type Icon -->
                            <div class="mt-1 flex-shrink-0">
                                @if (isset($notification->data['type']) && $notification->data['type'] === 'order')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>

                            <!-- Notification Content -->
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                <!-- Action Links -->
                                @if (isset($notification->data['actionUrl']))
                                    <div class="mt-2 flex justify-end space-x-2">
                                        <a
                                            href="{{ $notification->data['actionUrl'] }}"
                                            wire:click.stop="markAsRead('{{ $notification->id }}')"
                                            class="text-xs px-2 py-1 rounded-md bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-800 dark:text-amber-200 dark:hover:bg-amber-700 transition"
                                            role="menuitem"
                                            tabindex="-1"
                                        >
                                            {{ $notification->data['actionText'] ?? 'View' }}
                                        </a>

                                        <button
                                            wire:click.stop="markAsRead('{{ $notification->id }}')"
                                            class="text-xs px-2 py-1 rounded-md bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600 transition"
                                            role="menuitem"
                                            tabindex="-1"
                                        >
                                            Dismiss
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">No new notifications</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
