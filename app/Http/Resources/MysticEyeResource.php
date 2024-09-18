<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MysticEye */
class MysticEyeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "passive" => $this->passive,
            "active" => $this->active,
            "cooldown" => $this->cooldown,
            "current_cooldown" => $this->pivot_current_cooldown,
            "cost" => $this->cost
        ];
    }
}
