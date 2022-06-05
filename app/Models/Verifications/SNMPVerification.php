<?php

namespace App\Models\Verifications;

use App\Multitenancy\Utility\Traits\BelongsToTenant;
use App\Multitenancy\Utility\Traits\ForTenants;
use App\Utils\Dkron\Models\BaseDkronVerification;
use App\Utils\Interfaces\SafeDelete;

class SNMPVerification extends BaseDkronVerification implements SafeDelete
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
            'oid' => $this->oid,
            'ipv6' => $this->ipv6,
            'port' => $this->port,
            'data_type' => $this->data_type,
            'community' => $this->community,
            'template' => $this->template,
            'version' => $this->version,
        ];

        return json_encode($data);
    }

    public function getVerificationType()
    {
        return 'snmp';
    }

    public function getMeasurementName()
    {
        return config('verification_settings.measurement_names.snmp');
    }
}
