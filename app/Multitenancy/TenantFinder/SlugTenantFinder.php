<?php

namespace App\Multitenancy\TenantFinder;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class SlugTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    public function findForRequest(Request $request): ?Tenant
    {
        $slug = Str::before($request->path(), '/');

        return $this->getTenantModel()::whereSlug($slug)->first();
    }
}
