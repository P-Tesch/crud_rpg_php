<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property \Illuminate\Database\Eloquent\Collection<int, ScriptureAbility> | array<int, ScriptureAbility> $scriptureAbilities
 * @mixin IdeHelperScripture
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
        "description",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsToMany<ScriptureAbility, $this>
     */
    public function scriptureAbilities(): BelongsToMany
    {
        return $this->belongsToMany(ScriptureAbility::class);
    }

    /**
     * @return BelongsTo<Sheet, $this>
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class);
    }

    /**
     * The Lua code run as the action's strategy.
     *
     * @return BelongsTo<Strategy, $this>
     */
    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }
}
