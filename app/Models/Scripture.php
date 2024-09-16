<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property \Illuminate\Database\Eloquent\Collection<ScriptureAbility> | array $scriptureAbilities
 */
class Scripture extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "creation_points",
        "remaining_scripture_points",
        "damage",
        "range",
        "sharpness",
        "double",
        "description"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<ScriptureAbility> */
    public function scriptureAbilities() : BelongsToMany {
        return $this->belongsToMany(ScriptureAbility::class);
    }

    /** @return BelongsToMany<Sheet> */
    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }
}
