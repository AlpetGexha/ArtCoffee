<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The order instance.
     */
    protected Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $customer = $this->order->user ? $this->order->user->name : 'Guest';

        return [
            'title' => 'New Order Received',
            'message' => "Order #{$this->order->order_number} was placed by {$customer}.",
            'type' => 'order',
            'actionUrl' => route('filament.admin.resources.orders.edit', $this->order),
            'actionText' => 'View Order',
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
