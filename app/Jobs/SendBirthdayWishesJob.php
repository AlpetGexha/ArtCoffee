<?php

namespace App\Jobs;

use App\Mail\BirthdayWishesMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

final class SendBirthdayWishesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return "birthday_email_{$this->user->id}_" . now()->format('Y-m-d');
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Early return if the user no longer exists or email is unverified
        if (!$this->user->exists || $this->user->email_verified_at === null) {
            Log::warning("Skipping birthday email for user {$this->user->id}: User deleted or unverified");
            return;
        }

        // Early return if it's not actually the user's birthday (date might have changed)
        $today = now();
        $birthDate = $this->user->date_of_birth;

        if (!$birthDate || $birthDate->month !== $today->month || $birthDate->day !== $today->day) {
            Log::warning("Skipping birthday email for user {$this->user->id}: Not their birthday today");
            return;
        }

        try {
            // Prepare personalized special offer
            $age = $birthDate->age;
            $specialOffer = match(true) {
                $age >= 60 => "Enjoy a FREE coffee and pastry on your {$age}th birthday! Just show this email at any of our locations.",
                $age >= 30 => "Enjoy a FREE coffee on your {$age}th birthday! Just show this email at any of our locations.",
                default => "Enjoy a FREE coffee on your birthday! Just show this email at any of our locations.",
            };

            // Send birthday email to the user
            Mail::to($this->user->email)
                ->queue(new BirthdayWishesMail($this->user, $specialOffer));

            Log::info("Birthday email queued for {$this->user->email}");

        } catch (\Exception $e) {
            Log::error("Failed to send birthday email to {$this->user->email}", [
                'exception' => $e->getMessage(),
                'user_id' => $this->user->id,
            ]);

            // Re-throw the exception to trigger job retry
            throw $e;
        }
    }
}
