<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Stat */
class StatResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request) : array
    {
        return [
            "key" => $this->key,
            "value" => $this->value
        ];
    }
}
