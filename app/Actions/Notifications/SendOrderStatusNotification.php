<?php

namespace App\Actions\Notifications;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\User;

final class SendOrderStatusNotification
{
    protected SendDatabaseNotification $sendNotification;

    public function __construct(SendDatabaseNotification $sendNotification)
    {
        $this->sendNotification = $sendNotification;
    }

    /**
     * Send an order status notification to a user.
     */
    public function handle(Order $order, ?OrderStatus $previousStatus = null): void
    {
        if (! $order->user) {
            return;
        }

        // Get title and message based on order status
        [$title, $message] = $this->getNotificationContent($order, $previousStatus);

        // Generate action URL for the order tracking
        $actionUrl = route('orders.track', ['orderId' => $order->id]);

        // Send notification
        $this->sendNotification->handle(
            notifiable: $order->user,
            title: $title,
            message: $message,
            type: 'order',
            actionText: 'Track Order',
            actionUrl: $actionUrl
        );
    }

    /**
     * Get notification content based on order status.
     *
     * @return array<string, string> [title, message]
     */
    private function getNotificationContent(Order $order, ?OrderStatus $previousStatus): array
    {
        return match ($order->status) {
            OrderStatus::COMPLETED => [
                'Order Completed',
                "Your order #{$order->id} has been completed. Thank you for your business!",
            ],
            OrderStatus::PROCESSING => [
                'Order Processing',
                "Your order #{$order->id} is now being prepared.",
            ],
            OrderStatus::PENDING => [
                'Order Received',
                "Your order #{$order->id} has been received and is pending processing.",
            ],
            OrderStatus::CANCELLED => [
                'Order Cancelled',
                "Your order #{$order->id} has been cancelled.",
            ],
            default => [
                'Order Update',
                "Your order #{$order->id} has been updated to {$order->status->value}.",
            ]
        };
    }
}
