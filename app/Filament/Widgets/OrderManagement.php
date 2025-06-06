<?php

namespace App\Filament\Widgets;

use App\Actions\Notifications\SendOrderStatusNotification;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

final class OrderManagement extends Widget
{
    /**
     * The orders for display in the widget.
     */
    public ?Collection $orders = null;

    /**
     * Filter for order status.
     */
    public string $statusFilter = 'all';

    /**
     * Filter for today's orders.
     */
    public bool $todayOnly = false;

    /**
     * Sort direction for orders.
     */
    public string $sortDirection = 'asc'; // 'asc' for oldest first, 'desc' for newest first
    protected static ?int $sort = 1;

    protected static ?string $heading = 'Active Orders';

    /**
     * {@inheritDoc}
     */
    protected static string $view = 'filament.widgets.order-management';

    protected int|string|array $columnSpan = 'full';

    /**
     * Mount the widget and load initial data.
     */
    public function mount(): void
    {
        $this->loadOrders();
    }

    /**
     * Auto-refresh poll method that runs every 10 seconds.
     */
    #[On('refresh-orders')]
    public function refreshOrders(): void
    {
        $this->loadOrders();
    }

    /**
     * Load orders based on filters.
     */
    public function loadOrders(): void
    {
        $query = Order::query()
            ->with([
                'user',
                'items.product',
                'items.orderItemCustomizations.productOption',
            ]);

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        } else {
            // Default to showing only pending, processing, and ready orders
            $query->whereIn('status', [
                OrderStatus::PENDING->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::READY->value,
            ]);
        }

        // Apply today filter
        if ($this->todayOnly) {
            $query->whereDate('created_at', Carbon::today());
        }

        // Apply sorting (default to oldest first, for FIFO processing)
        $query->orderBy('created_at', $this->sortDirection);

        $this->orders = $query->take(9)->get();
    }

    /**
     * Toggle the sort direction.
     */
    public function toggleSortDirection(): void
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->loadOrders();
    }

    /**
     * Update the status filter and reload orders.
     */
    public function filterByStatus(string $status): void
    {
        $this->statusFilter = $status;
        $this->loadOrders();
    }

    /**
     * Toggle today's orders filter.
     */
    public function toggleTodayFilter(): void
    {
        $this->todayOnly = ! $this->todayOnly;
        $this->loadOrders();
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, OrderStatus $status): void
    {
        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        $order->status = $status->value;
        $order->save();

        app(SendOrderStatusNotification::class)->handle($order);
        event(new OrderStatusUpdated($order));

        // Reload orders to refresh the view
        $this->loadOrders();
    }

    /**
     * Mark order as ready for pickup.
     */
    public function markAsReady(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::READY);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::PROCESSING);
    }

    /**
     * Mark order as completed (picked).
     */
    public function markAsPicked(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::COMPLETED);
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::READY);
    }

    /**
     * Cancel order.
     */
    public function cancelOrder(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::CANCELLED);
    }

    /**
     * Update payment status to paid.
     */
    public function markAsPaid(int $orderId): void
    {
        $order = Order::find($orderId);

        if (! $order) {
            return;
        }

        $order->payment_status = PaymentStatus::PAID->value;
        $order->save();

        $this->loadOrders();
    }

    public function markAsConfirm(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::COMPLETED);
    }
    // markAsProgrees

    public function markAsProgrees(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::PROCESSING);
    }

    /**
     * Get the grid configuration for the widget layout.
     */
    protected function getGridColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
        ];
    }
}
