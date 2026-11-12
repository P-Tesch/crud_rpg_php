<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSystem
 */
class System extends Model
{
    /** @use HasFactory<\Database\Factories\SystemFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "damage",
        "strategy_passive",
        "strategy_active",
        "strategy_burn",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_passive",
        "strategy_active",
        "strategy_burn",
    ];

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheets(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class);
    }

    /**
     * @return BelongsToMany<Effect, $this>
     */
    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(Effect::class);
    }

    /**
     * @return HasMany<Subsystem, $this>
     */
    public function subsystems(): HasMany
    {
        return $this->hasMany(Subsystem::class);
    }
}
