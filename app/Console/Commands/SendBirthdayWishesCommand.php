<?php

namespace App\Console\Commands;

use App\Jobs\SendBirthdayWishesJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class SendBirthdayWishesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-birthday-wishes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday wishes to users who have their birthday today';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting to process birthday wishes...');

        $chunkSize = 50;
        $today = now();

        try {
            // Find users with birthdays today using query builder with clean syntax
            $query = User::query()
                ->whereMonth('date_of_birth', $today->month)
                ->whereDay('date_of_birth', $today->day)
                ->whereNotNull('email_verified_at');
                // dd($query->toRawSql());

            $totalUsers = $query->count();
            $this->info("Found {$totalUsers} users with birthdays today");

            if ($totalUsers === 0) {
                $this->info('No birthdays today. Exiting.');
                return self::SUCCESS;
            }

            // Process users in chunks for memory efficiency
            $this->withProgressBar($query->cursor(), function (User $user): void {
                // Dispatch job to queue
                SendBirthdayWishesJob::dispatch($user);
            });

            $this->newLine(2);
            $this->info('Birthday wishes processing completed!');
            Log::info("Birthday wishes scheduled for {$totalUsers} users");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error processing birthday wishes: {$e->getMessage()}");
            Log::error("Birthday wishes command failed", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return self::FAILURE;
        }
    }
}
