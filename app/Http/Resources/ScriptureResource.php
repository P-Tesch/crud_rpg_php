<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Scripture */
class ScriptureResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "creation_points" => $this->creation_points,
            "remaining_scripture_points" => $this->remaining_scripture_points,
            "damage" => $this->damage,
            "range" => $this->range,
            "sharpness" => $this->sharpness,
            "double" => $this->double,
            "scripture_abilities" => ScriptureAbilityResource::collection($this->scriptureAbilities),
            "description" => $this->description
        ];
    }
}
