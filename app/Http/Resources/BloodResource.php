<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Blood */
class BloodResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "impulses" => $this->impulses,
            "stats" => StatResource::collection($this->stats),
            "blood_abilities" => BloodAbilityResource::collection($this->bloodAbilities),
        ];
    }
}
