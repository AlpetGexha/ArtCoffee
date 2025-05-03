<?php

namespace App\Actions\Notifications;

use App\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Model;

class SendDatabaseNotification
{
    /**
     * Send a database notification to a user.
     *
     * @param \Illuminate\Database\Eloquent\Model $notifiable The user or entity to notify
     * @param string $title The notification title
     * @param string $message The notification message
     * @param string|null $type The notification type (e.g., 'order', 'payment', etc.)
     * @param string|null $actionText The text for the action button
     * @param string|null $actionUrl The URL for the action button
     * @return void
     */
    public function handle(
        Model $notifiable,
        string $title,
        string $message,
        ?string $type = null,
        ?string $actionText = null,
        ?string $actionUrl = null
    ): void {
        // Guard against non-notifiable models
        if (!method_exists($notifiable, 'notify')) {
            return;
        }

        $notification = new DatabaseNotification(
            title: $title,
            message: $message,
            type: $type,
            actionText: $actionText,
            actionUrl: $actionUrl
        );

        $notifiable->notify($notification);
    }
}
