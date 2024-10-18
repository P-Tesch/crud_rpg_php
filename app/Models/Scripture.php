<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property \Illuminate\Database\Eloquent\Collection<int, ScriptureAbility> | array<int, ScriptureAbility> $scriptureAbilities
 */
class Scripture extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
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

    /** @return BelongsToMany<ScriptureAbility> */
    public function scriptureAbilities() : BelongsToMany {
        return $this->belongsToMany(ScriptureAbility::class);
    }

    /** @return BelongsTo<Sheet, Scripture> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }

    /** @return BelongsTo<Strategy, Scripture> */
    public function strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
