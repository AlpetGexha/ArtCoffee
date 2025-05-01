<?php

namespace App\Jobs;

use App\Mail\GiftCardMail;
use App\Models\GiftCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendGiftCardEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected GiftCard $giftCard
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Skip if no recipient email
        if (empty($this->giftCard->recipient_email)) {
            return;
        }

        // Send email notification to recipient
        Mail::to($this->giftCard->recipient_email)
            ->send(new GiftCardMail($this->giftCard));
    }
}
