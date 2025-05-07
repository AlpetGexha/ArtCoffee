<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class DatabaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification data.
     */
    protected array $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $title,
        string $message,
        ?string $type = null,
        ?string $actionText = null,
        ?string $actionUrl = null
    ) {
        $this->notificationData = [
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ];

        if ($actionText && $actionUrl) {
            $this->notificationData['actionText'] = $actionText;
            $this->notificationData['actionUrl'] = $actionUrl;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->notificationData;
    }
}
