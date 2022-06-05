<?php

namespace App\Multitenancy\Tasks;

// use App\Models\Tenant;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SetTimezoneTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        $this->setTimezone($tenant->timezone);
    }

    public function forgetCurrent(): void
    {
        $this->setTimezone(null);
    }

    protected function setTimezone(?string $timezone): void
    {
        session()->put('timezone', $timezone);
    }
}
