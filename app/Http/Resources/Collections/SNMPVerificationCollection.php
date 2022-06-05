<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SNMPVerificationCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Models\SNMPVerification';

    /**
     * Transform the resource collection into an array.
     *
     * @psalm-suppress all
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection;
    }
}
