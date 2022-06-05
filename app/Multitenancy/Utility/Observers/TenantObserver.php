<?php

namespace App\Multitenancy\Utility\Observers;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Tenant;

class TenantObserver
{
    /**
     * The tenant model.
     *
     * @var Tenant
     */
    protected $tenant;

    /**
     * Create the observer.
     *
     * @param Model $tenant
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * When creating.
     *
     * @param  Model  $model
     * @return void
     */
    public function creating(Model $model)
    {
        $foreignKey = $this->getForeignKey();

        if (! isset($model->{$foreignKey})) {
            $model->setAttribute($foreignKey, $this->tenant->id);
        }
    }

    /**
     * Get foreign key.
     *
     * @return string
     */
    protected function getForeignKey()
    {
        return $this->tenant->getForeignKey();
    }
}
