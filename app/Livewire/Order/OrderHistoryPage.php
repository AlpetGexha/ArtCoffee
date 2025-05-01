<?php

namespace App\Livewire\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class OrderHistoryPage extends Component
{
    use WithPagination;
    
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    
    public function render(): View
    {
        return view('livewire.order.order-history-page', [
            'orders' => $this->orders,
        ]);
    }
    
    #[Computed]
    public function orders()
    {
        if (!auth()->check()) {
            return collect([]);
        }
        
        $query = Order::query()
            ->where('user_id', auth()->id())
            ->with(['items.product', 'branch']);
            
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        return $query->orderBy($this->sortField, $this->sortDirection)
            ->simplePaginate(10);
    }
    
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        // Reset pagination when sorting
        $this->resetPage();
    }
    
    public function filterByStatus(?string $status): void
    {
        $this->status = $status ?? '';
        $this->resetPage();
    }
    
    public function resetFilters(): void
    {
        $this->status = '';
        $this->resetPage();
    }
}
