<?php

namespace App\Providers;

use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use App\Observers\DriverObserver;
use App\Observers\PermitObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Driver::observe(DriverObserver::class);
        Permit::observe(PermitObserver::class);
        User::observe(UserObserver::class);

        Gate::define('viewReports', function (User $user): bool {
            return $user->hasRole(User::ROLE_ADMIN, User::ROLE_FLEET_OFFICER, User::ROLE_MANAGEMENT);
        });

        Gate::define('viewAuditLogs', fn (User $user): bool => $user->hasRole(User::ROLE_ADMIN));
    }
}
