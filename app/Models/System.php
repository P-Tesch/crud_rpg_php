<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class System extends Model
{
    /** @use HasFactory<\Database\Factories\SystemFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "damage"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy_passive",
        "strategy_active",
        "strategy_burn"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }

    /** @return BelongsToMany<Effect> */
    public function effects() : BelongsToMany {
        return $this->belongsToMany(Effect::class);
    }

    /** @return HasMany<Subsystem> */
    public function subsystems() : HasMany {
        return $this->hasMany(Subsystem::class);
    }
}
