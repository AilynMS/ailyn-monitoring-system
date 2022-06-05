<?php

namespace App\Http\Resources\Models;

use App\Http\Resources\Models\Tenant as TenantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ICMPVerification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'token' => $this->token,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'ipv6' => $this->ipv6,
            'target' => $this->target,
            'interval' => $this->interval,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tenant' => new TenantResource($this->whenLoaded('tenant')),
        ];
    }
}
