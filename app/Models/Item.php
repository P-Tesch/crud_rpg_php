<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperItem
 */
class Item extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "damage",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsToMany<Effect, $this>
     */
    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(Effect::class)->withPivot("remaining_duration", "power");
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
