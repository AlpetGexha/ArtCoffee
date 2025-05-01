<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Enum\OrderStatus;
use Livewire\Attributes\Url;
use Illuminate\Support\Carbon;

class OrderTrackingPage extends Component
{
    #[Url]
    public ?string $orderId = null;

    public ?Order $currentOrder = null;

    public function mount(?string $id = null)
    {
        $this->orderId = $id;
        $this->loadCurrentOrder();
    }

    public function loadCurrentOrder()
    {
        if ($this->orderId) {
            // Load the specific order being tracked
            $this->currentOrder = Order::with([
                'items.product', 
                'items.customizations.productOption', 
                'branch'
            ])->find($this->orderId);

            // If order not found or not accessible to this user, reset
            if (!$this->currentOrder || 
                (!auth()->check() && !session()->has('guest_order_' . $this->orderId))) {
                $this->currentOrder = null;
            }
        } elseif (auth()->check()) {
            // Load the most recent in-progress order for authenticated user
            $this->currentOrder = auth()->user()->orders()
                ->with([
                    'items.product', 
                    'items.customizations.productOption', 
                    'branch'
                ])
                ->inProgress()
                ->latest()
                ->first();
        } else {
            // No specific order and no authenticated user
            $this->currentOrder = null;
        }
    }

    public function refresh()
    {
        $this->loadCurrentOrder();
    }

    public function confirmPickup()
    {
        if ($this->currentOrder && $this->currentOrder->status === OrderStatus::READY) {
            $this->currentOrder->update([
                'status' => OrderStatus::COMPLETED,
                'completed_at' => now()
            ]);
            
            $this->refresh();
        }
    }

    public function getOrderProgressPercentage()
    {
        if (!$this->currentOrder) {
            return 0;
        }

        return match ($this->currentOrder->status->value) {
            'pending' => 25,
            'processing' => 50,
            'ready' => 75,
            'completed' => 100,
            'cancelled' => 0,
            default => 0,
        };
    }

    public function getEstimatedReadyTime()
    {
        if (!$this->currentOrder) {
            return 'Unknown';
        }

        // Logic to calculate estimated ready time based on order complexity, business rules, etc.
        // This is a simplified version
        $baseMinutes = 10; // Base preparation time

        // Add time based on number of items
        $itemCount = $this->currentOrder->items->sum('quantity');
        $additionalMinutes = min(20, $itemCount * 3); // Cap at 20 minutes additional
        
        // Calculate total time in minutes
        $totalMinutes = $baseMinutes + $additionalMinutes;
        
        // Calculate estimated ready time
        $estimatedReadyTime = Carbon::parse($this->currentOrder->created_at)
            ->addMinutes($totalMinutes);
            
        // If already past the estimated time, show "Soon"
        if ($estimatedReadyTime->isPast()) {
            return 'Soon';
        }
        
        // If less than an hour from now, show "X mins"
        if ($estimatedReadyTime->diffInHours(now()) < 1) {
            return $estimatedReadyTime->diffInMinutes(now()) . ' mins';
        }
        
        // Otherwise show the time
        return $estimatedReadyTime->format('g:i A');
    }

    public function getInProgressOrdersProperty()
    {
        if (!auth()->check()) {
            return collect();
        }

        return auth()->user()->orders()
            ->inProgress()
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.order.order-tracking-page');
    }
}
