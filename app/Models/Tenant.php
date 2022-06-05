<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenantModel;

class Tenant extends BaseTenantModel
{
    protected $guarded = [];
}
