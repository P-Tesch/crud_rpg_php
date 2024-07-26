<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SheetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "portrait" => $this->portrait,
            "description" => $this->description,
            "background" => $this->background,
            "creation_points" => $this->creation_points,
            "alignment" => $this->alignment,
            "organization" => $this->organization,
            "stats" => StatResource::collection($this->stats),
            "attributes" => RpgAttributeResource::collection($this->rpgAttributes),
            "skills"=> SkillResource::collection($this->skills),
            "advantages"=> AdvantageResource::collection($this->advantages),
            "blood" => new BloodResource($this->blood),
            "items" => ItemResource::collection($this->items),
            "effects" => EffectResource::collection($this->effects),
            "miracles" => MiracleResource::collection($this->miracles),
            "mystic_eyes" => MysticEyeResource::collection($this->mysticEyes),
            "schools" => SchoolResource::collection($this->schools),
            "scripture" => new ScriptureResource($this->scripture),
            "sonatas" => SonataResource::collection($this->sonatas),
        ];
    }
}
