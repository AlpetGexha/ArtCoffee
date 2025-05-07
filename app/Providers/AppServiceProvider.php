<?php

namespace App\Providers;

use App\Services\LoyaltyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {
        $this->app->singleton(LoyaltyService::class, function ($app) {
            return new LoyaltyService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureEloquent();
        $this->configureDatabase();
        $this->configureDate();
        $this->configureSchema();

        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });
    }

    private function configureEloquent(): void
    {
        Model::shouldBeStrict(!app()->isProduction());
        Model::automaticallyEagerLoadRelationships();
    }

    private function configureDatabase(): void
    {
        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
    }

    private function configureDate(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureSchema(): void
    {
        URL::forceHttps(
            app()->isProduction(),
        );
    }
}
