<?php

namespace App\Providers;

use App\Multitenancy\Utility\Observers\TenantObserver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class MultiTenancyProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('tenant', function () {
            $this->unsignedBigInteger('tenant_id');

            return $this->foreign('tenant_id')
                ->references('id')
                ->on('tenants');
        });

        $this->app->singleton(TenantObserver::class, function () {
            return new TenantObserver(app('currentTenant'));
        });
    }
}
