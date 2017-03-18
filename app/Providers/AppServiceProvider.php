<?php

namespace App\Providers;

use App\Core\Billing\PaymentGatewayInterface;
use App\Core\Billing\StripePaymentGateway;
use App\Core\Billing\TestPaymentGateway;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);
    }
}
