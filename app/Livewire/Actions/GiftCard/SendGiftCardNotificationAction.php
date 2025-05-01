<?php

namespace App\Livewire\Actions\GiftCard;

use App\Models\GiftCard;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

final class SendGiftCardNotificationAction implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the action to send a gift card notification email.
     */
    public function handle(GiftCard $giftCard): bool
    {
        // Skip if no recipient email
        if (empty($giftCard->recipient_email)) {
            return false;
        }

        try {
            // Send email notification to recipient
            Mail::to($giftCard->recipient_email)
                ->send(new GiftCardMailable($giftCard));

            return true;
        } catch (Exception $e) {
            report($e); // Log the error

            return false;
        }
    }
}

/**
 * Mailable for gift card notifications.
 */
final class GiftCardMailable extends Mailable
{
    public function __construct(public GiftCard $giftCard) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        $subject = 'You received a gift card';

        if ($this->giftCard->occasion) {
            $subject .= ' for your ' . $this->giftCard->occasion;
        }

        return $this->subject($subject)
            ->markdown('emails.gift-card', [
                'giftCard' => $this->giftCard,
                'amount' => '$' . number_format($this->giftCard->amount, 2),
                'senderName' => $this->giftCard->sender->name ?? 'Someone',
                'message' => $this->giftCard->message,
                'expirationDate' => $this->giftCard->expires_at->format('M j, Y'),
                'redeemUrl' => route('gift-cards.redeem', ['code' => $this->giftCard->activation_key]),
            ]);
    }
}
