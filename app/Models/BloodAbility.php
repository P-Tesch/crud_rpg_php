<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperBloodAbility
 */
class BloodAbility extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsTo<Blood, $this>
     */
    public function blood(): BelongsTo
    {
        return $this->belongsTo(Blood::class);
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
