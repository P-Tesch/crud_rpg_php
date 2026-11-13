<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property ?int $pivot_remaining_duration
 * @property ?int $pivot_power
 * @mixin IdeHelperEffect
 */
class Effect extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "remaining_duration",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsToMany<Item, $this>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot("remaining_duration", "power");
    }

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheets(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class)->withPivot("remaining_duration", "power");
    }

    /**
     * @return BelongsToMany<System, $this>
     */
    public function systems(): BelongsToMany
    {
        return $this->belongsToMany(System::class)->withPivot("remaining_duration", "power");
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
