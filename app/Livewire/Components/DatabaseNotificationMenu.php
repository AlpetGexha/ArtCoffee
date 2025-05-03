<?php

namespace App\Livewire\Components;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class DatabaseNotificationMenu extends Component
{
    /**
     * The unread notifications.
     */
    public Collection $notifications;

    /**
     * Whether the notifications dropdown is open.
     */
    public bool $isOpen = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->refreshNotifications();
    }

    /**
     * Refresh notifications on the polling interval.
     */
    #[On('refresh-notifications')]
    public function refreshNotifications(): void
    {
        $this->notifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->get();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        $this->refreshNotifications();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        $this->refreshNotifications();
    }

    /**
     * Toggle the notification dropdown.
     */
    public function toggleDropdown(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.components.database-notification-menu');
    }
}
