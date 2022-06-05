<?php

namespace App\Multitenancy\Utility\Traits;

use App\Multitenancy\Utility\Observers\TenantObserver;
use App\Multitenancy\Utility\Scopes\TenantScope;
use Spatie\Multitenancy\Models\Tenant;

trait ForTenants
{
    public static function boot()
    {
        parent::boot();

        if ($tenant = Tenant::current()) {
            static::addGlobalScope(
                new TenantScope($tenant)
            );
            static::observe(
                app()->make(TenantObserver::class)
            );
        }
    }
}
