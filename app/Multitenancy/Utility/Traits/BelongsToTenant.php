<?php

namespace App\Multitenancy\Utility\Traits;

use App\Models\Tenant;

trait BelongsToTenant
{
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
