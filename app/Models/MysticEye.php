<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $pivot_current_cooldown
 * @mixin IdeHelperMysticEye
 */
class MysticEye extends Model
{
    /** @use HasFactory<\Database\Factories\MysticEyeFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "passive",
        "active",
        "cooldown",
        "pivot_current_cooldown",
        "cost",
    ];

    /** @var list<string> */
    protected $hidden = [
        "passive_strategy_id",
        "passive_strategy",
        "active_strategy_id",
        "active_strategy",
    ];

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheet(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class)->withPivot("current_cooldown");
    }

    /**
     * The Lua code run as the passive strategy.
     *
     * @return BelongsTo<Strategy, $this>
     */
    public function passiveStrategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }

    /**
     * The Lua code run as the active strategy.
     *
     * @return BelongsTo<Strategy, $this>
     */
    public function activeStrategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }
}
