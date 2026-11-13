<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperSubsystem
 */
class Subsystem extends Model
{
    /** @use HasFactory<\Database\Factories\SubsystemFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "system_id",
    ];

    /** @var list<string> */
    protected $hidden = [
        "passive_strategy_id",
        "passive_strategy",
        "active_strategy_id",
        "active_strategy",
        "burn_strategy_id",
        "burn_strategy",
    ];

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheets(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class)->withPivot("burn_duration");
    }

    /**
     * @return BelongsTo<System, $this>
     */
    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
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

    /**
     * The Lua code run as the burn strategy.
     *
     * @return BelongsTo<Strategy, $this>
     */
    public function burnStrategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }
}
