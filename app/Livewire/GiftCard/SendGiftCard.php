<?php

namespace App\Livewire\GiftCard;

use App\Jobs\SendGiftCardEmailJob;
use App\Livewire\Actions\GiftCard\SendGiftCardNotificationAction;
use App\Models\GiftCard;
use App\Models\User;
use Illuminate\Validation\Rules\Email;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

final class SendGiftCard extends Component
{
    #[Rule('required|numeric|min:10|max:1000')]
    public float $amount = 25.00;

    #[Rule('required|email')]
    public string $recipientEmail = '';

    #[Rule('nullable|string|max:200')]
    public ?string $message = null;

    #[Rule('nullable|string|max:50')]
    public ?string $occasion = null;

    public bool $showSuccessMessage = false;
    public ?GiftCard $createdGiftCard = null;

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.gift-card.send-gift-card', [
            'occasions' => [
                'birthday' => 'Birthday',
                'anniversary' => 'Anniversary',
                'thank you' => 'Thank You',
                'holiday' => 'Holiday',
                'congratulations' => 'Congratulations',
                'other' => 'Other',
            ],
        ]);
    }

    /**
     * Send the gift card.
     */
    public function send(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));

            return;
        }

        $this->validate();

        $user = auth()->user();

        // Prevent users from sending gift cards to themselves
        if ($user->email === $this->recipientEmail) {
            $this->addError('recipientEmail', 'You cannot send a gift card to yourself.');
            return;
        }

        // Check if user has sufficient balance
        if ($user->balanceFloat < $this->amount) {
            $this->addError('amount', 'Insufficient balance to send this gift card.');

            return;
        }

        // Find recipient in the system or create gift card for email address
        $recipientId = null;
        $recipient = User::where('email', $this->recipientEmail)->first();

        if ($recipient) {
            $recipientId = $recipient->id;
        }

        // Create the gift card
        $giftCard = GiftCard::create([
            'sender_id' => $user->id,
            'recipient_id' => $recipientId,
            'recipient_email' => $this->recipientEmail,
            'amount' => $this->amount,
            'message' => $this->message,
            'occasion' => $this->occasion,
            'is_active' => true,
            'expires_at' => now()->addYear(),
        ]);

        // Withdraw amount from user's wallet
        $user->withdrawFloat($this->amount, [
            'description' => 'Gift card purchase',
            'gift_card_id' => $giftCard->id,
        ]);

        // Store the created gift card to display details
        $this->createdGiftCard = $giftCard;
        $this->showSuccessMessage = true;

        // Dispatch the job to send the email notification
        SendGiftCardEmailJob::dispatch($giftCard);

        // Reset form
        $this->reset(['amount', 'recipientEmail', 'message', 'occasion']);
    }
}
