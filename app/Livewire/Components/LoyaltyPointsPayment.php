<?php

namespace App\Livewire\Components;

use App\Services\LoyaltyService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

final class LoyaltyPointsPayment extends Component
{
    public bool $usePoints = false;
    public int $pointsBalance = 0;
    public float $orderTotal = 0;
    public int $requiredPoints = 0;

    public function mount(float $orderTotal)
    {
        $this->orderTotal = $orderTotal;
        $this->updatePointsData();
    }

    public function render()
    {
        $loyaltyService = app(LoyaltyService::class);
        $pointsValueFormatted = $loyaltyService->formatPointsAsDollars($this->pointsBalance);
        $requiredValueFormatted = $loyaltyService->formatPointsAsDollars($this->requiredPoints);
        $hasEnoughPoints = $this->pointsBalance >= $this->requiredPoints;

        return view('livewire.components.loyalty-points-payment', [
            'pointsBalance' => $this->pointsBalance,
            'pointsValueFormatted' => $pointsValueFormatted,
            'requiredPoints' => $this->requiredPoints,
            'requiredValueFormatted' => $requiredValueFormatted,
            'hasEnoughPoints' => $hasEnoughPoints
        ]);
    }

    public function updatedUsePoints()
    {
        $this->dispatch('loyalty-payment-updated', [
            'usePoints' => $this->usePoints,
        ]);
    }

    private function updatePointsData(): void
    {
        // If user is logged in, get their points balance
        if (Auth::check()) {
            $this->pointsBalance = Auth::user()->loyalty_points ?? 0;

            // Calculate required points for this order
            $loyaltyService = app(LoyaltyService::class);
            $this->requiredPoints = $loyaltyService->calculatePointsEarned($this->orderTotal);
        }
    }
}
