<?php

namespace App\Livewire\GiftCard;

use App\Models\GiftCard;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Attributes\Rule;
use Livewire\Component;

final class RedeemGiftCard extends Component
{
    #[Rule('required|string|size:32')]
    #[Url]
    public string $activationKey = '';

    #[Url]
    public bool $autoRedeem = false;

    public bool $showForm = true;
    public bool $showSuccess = false;
    public bool $showError = false;
    public ?GiftCard $giftCard = null;
    public string $errorMessage = '';

    /**
     * Initialize the component with route parameters.
     */
    public function mount(?string $code = null): void
    {
        // Set activation key from route parameter if provided
        if ($code) {
            $this->activationKey = $code;
        }

        // Check if auto-redemption is requested via query param
        $this->autoRedeem = request()->boolean('auto_redeem', false);

        // Auto-redeem if requested and we have a code
        if ($this->autoRedeem && $this->activationKey && Auth::check()) {
            $this->redeem();
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.gift-card.redeem-gift-card');
    }

    /**
     * Attempt to redeem a gift card.
     */
    public function redeem(): void
    {
        // Validate user is authenticated
        if (! auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        $this->validate();

        try {
            // Find gift card by activation key
            $giftCard = GiftCard::byActivationKey($this->activationKey)->first();

            if (! $giftCard) {
                throw new ModelNotFoundException('Gift card not found with that activation key.');
            }

            // Get the current user
            $user = auth()->user();

            // Check if the user is the intended recipient when is_for_anyone is false
            if (!$giftCard->is_for_anyone && $giftCard->recipient_email !== $user->email) {
                throw new Exception('This gift card was sent specifically to ' . $giftCard->recipient_email . ' and cannot be redeemed by your account.');
            }

            // Check if gift card is valid
            if (! $giftCard->isValid()) {
                if ($giftCard->isRedeemed()) {
                    throw new Exception('This gift card has already been redeemed.');
                }

                if ($giftCard->isExpired()) {
                    throw new Exception('This gift card has expired.');
                }

                if (! $giftCard->is_active) {
                    throw new Exception('This gift card is inactive.');
                }

                throw new Exception('This gift card cannot be redeemed.');
            }

            // Process redemption
            $giftCard->redeem($user);

            // Add amount to user's wallet
            $user->depositFloat($giftCard->amount, [
                'description' => 'Gift card redemption',
                'gift_card_id' => $giftCard->id,
            ]);

            // Set success state
            $this->giftCard = $giftCard;
            $this->showSuccess = true;
            $this->showForm = false;

        } catch (ModelNotFoundException $e) {
            $this->showError = true;
            $this->errorMessage = "We couldn't find a gift card with that activation code. Please check and try again.";
        } catch (Exception $e) {
            $this->showError = true;
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Reset form state.
     */
    public function resetForm(): void
    {
        $this->reset(['activationKey', 'showError', 'errorMessage']);
    }
}
