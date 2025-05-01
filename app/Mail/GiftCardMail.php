<?php

namespace App\Mail;

use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class GiftCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GiftCard $giftCard) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'You received a gift card';

        if ($this->giftCard->occasion) {
            $subject .= ' for your ' . $this->giftCard->occasion;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.gift-card',
            with: [
                'giftCard' => $this->giftCard,
                'amount' => '$' . number_format($this->giftCard->amount, 2),
                'senderName' => $this->giftCard->sender->name ?? 'Someone',
                'message' => $this->giftCard->message,
                'expirationDate' => $this->giftCard->expires_at->format('M j, Y'),
                'redeemUrl' => route('gift-cards.redeem', ['activationKey' => $this->giftCard->activation_key, 'auto_redeem' => true]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
