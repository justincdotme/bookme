<?php

namespace App\Providers;

use App\Core\Payment\PaymentGatewayInterface;
use App\Core\Payment\StripePaymentGateway;
use App\Http\Composers\StateListComposer;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Set locale for currency
        setlocale(LC_MONETARY, 'en_US.UTF-8');

        //Configure StripePaymentGateway
        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        //Bind in StripePaymentGateway
        $this->app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);

        //Register Laravel Dusk
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        //Register Laravel Debug Bar
        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        //Send the State list for use in searches
        view()->composer([
            'public.home',
            'public.properties.search'
        ], StateListComposer::class);
    }
}
