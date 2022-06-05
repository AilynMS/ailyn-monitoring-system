<?php

namespace App\Models\Verifications;

use App\Multitenancy\Utility\Traits\BelongsToTenant;
use App\Multitenancy\Utility\Traits\ForTenants;
use App\Utils\Dkron\Models\BaseDkronVerification;
use App\Utils\Interfaces\SafeDelete;

class ICMPVerification extends BaseDkronVerification implements SafeDelete
{
    use ForTenants, BelongsToTenant;

    protected $guarded = [];

    public function isDeleteable()
    {
        return true; // All verifications are deleteable for now
    }

    public function getJobPayload()
    {
        $data = [
            'tenant_id' => $this->tenant_id,
            'uid' => $this->token,
            'target' => $this->target,
            'ipv6' => $this->ipv6,
        ];

        return json_encode($data);
    }

    public function getVerificationType()
    {
        return 'icmp';
    }

    public function getMeasurementName()
    {
        return config('verification_settings.measurement_names.icmp');
    }
}
