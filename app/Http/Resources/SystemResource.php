<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\System */
class SystemResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request) : array
    {
        return [
            "name" => $this->name,
            "description" => $this->description,
            "damage" => $this->damage,
            "subsystems" => SubsystemResource::collection($this->subsystems)
        ];
    }
}
