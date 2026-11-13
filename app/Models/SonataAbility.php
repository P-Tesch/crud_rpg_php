<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSonataAbility
 */
class SonataAbility extends Model
{
    /** @use HasFactory<\Database\Factories\SonataAbilityFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "level",
        "cost",
        "sonata_id",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsTo<Sonata, $this>
     */
    public function sonata(): BelongsTo
    {
        return $this->belongsTo(Sonata::class);
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
