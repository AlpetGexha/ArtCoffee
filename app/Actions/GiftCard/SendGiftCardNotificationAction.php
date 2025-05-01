<?php

namespace App\Actions\GiftCard;

use App\Mail\GiftCardMail;
use App\Models\GiftCard;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendGiftCardNotificationAction implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the action to send a gift card notification email.
     */
    public function handle(GiftCard $giftCard): bool
    {
        if (empty($giftCard->recipient_email)) {
            return false;
        }

        try {
            Mail::to($giftCard->recipient_email)
                ->send(new GiftCardMail($giftCard));

            return true;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }
}
