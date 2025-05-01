<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register the schedule directly in the console.php file
// This approach works well with Laravel 12's simplified architecture
Schedule::command('app:send-birthday-wishes')
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/birthday-wishes.log'));


