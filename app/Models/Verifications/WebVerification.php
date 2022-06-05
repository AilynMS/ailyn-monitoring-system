<?php

namespace App\Models\Verifications;

use App\Multitenancy\Utility\Traits\BelongsToTenant;
use App\Multitenancy\Utility\Traits\ForTenants;
use App\Utils\Dkron\Models\BaseDkronVerification;
use App\Utils\Interfaces\SafeDelete;

class WebVerification extends BaseDkronVerification implements SafeDelete
{
    use ForTenants, BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'headers' => 'array',
        'response_codes' => 'array',
    ];

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
            'path' => $this->path,
            'ipv6' => $this->ipv6,
            'https' => $this->https,
            'response_codes' => $this->response_codes,
            'port' => $this->port,
            'headers' => $this->headers,
        ];

        return json_encode($data);
    }

    public function getVerificationType()
    {
        return 'web';
    }

    public function getMeasurementName()
    {
        return config('verification_settings.measurement_names.web');
    }
}
