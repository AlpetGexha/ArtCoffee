<?php

namespace App\Filament\Widgets;

use App\Actions\Notifications\SendOrderStatusNotification;
use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class OrderManagement extends Widget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Active Orders';

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

    /**
     * {@inheritDoc}
     */
    protected static string $view = 'filament.widgets.order-management';

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
     * Get the grid configuration for the widget layout.
     */
    protected function getGridColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 3
        ];
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
                'items.orderItemCustomizations.productOption'
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
        $this->todayOnly = !$this->todayOnly;
        $this->loadOrders();
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            return;
        }

        $order->status = $status;
        $order->save();

        // Notify the customer about the order status change
        app(SendOrderStatusNotification::class)->handle($order);

        // Reload orders to refresh the view
        $this->loadOrders();
    }

    /**
     * Mark order as ready for pickup.
     */
    public function markAsReady(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::READY->value);
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::PROCESSING->value);
    }

    /**
     * Mark order as completed (picked).
     */
    public function markAsPicked(int $orderId): void
    {
        $this->updateOrderStatus($orderId, OrderStatus::COMPLETED->value);
    }

    /**
     * Update payment status to paid.
     */
    public function markAsPaid(int $orderId): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            return;
        }

        $order->payment_status = PaymentStatus::PAID->value;
        $order->save();

        $this->loadOrders();
    }
}
