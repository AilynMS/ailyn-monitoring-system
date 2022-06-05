<?php

namespace App\Multitenancy\Utility\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Multitenancy\Models\Tenant;

class TenantScope implements Scope
{
    /**
     * The tenant model.
     *
     * @var Tenant
     */
    protected $tenant;

    /**
     * Create the scope.
     *
     * @param Tenant $tenant
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Apply the scope.
     *
     * @param  Builder $builder
     * @param  Model   $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($this->tenant->getForeignKey(), '=', $this->tenant->id);
    }
}
