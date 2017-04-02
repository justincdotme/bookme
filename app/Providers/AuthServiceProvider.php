<?php

namespace App\Providers;

use App\Core\Reservation;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Reservation::class => ReservationPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('create-admin-user', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('update-user', function ($user, $userToUpdate) {
            return ($userToUpdate->id == $user->id);
        });

        Gate::define('show-user', function ($user, $userToShow) {
            return ($userToShow->id == $user->id);
        });

        Gate::define('show-reservations', function ($user, $userToShow) {
            return ($userToShow->id == $user->id);
        });
    }
}
