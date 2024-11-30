<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        "damage"
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

    /** @return BelongsTo<Strategy, System> */
    public function passive_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }

    /** @return BelongsTo<Strategy, System> */
    public function active_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }

    /** @return BelongsTo<Strategy, System> */
    public function burn_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
