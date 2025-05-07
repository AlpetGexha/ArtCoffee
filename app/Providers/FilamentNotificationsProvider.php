<?php

namespace App\Providers;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Notifications\Livewire\Notifications;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class FilamentNotificationsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Filament notification components with Livewire
        Livewire::component('notifications', Notifications::class);
        Livewire::component('database-notifications', DatabaseNotifications::class);
    }
}
