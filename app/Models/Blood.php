<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blood extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "impulses"
    ];

    /** @return BelongsTo<Sheet, Blood> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }

    /** @return HasMany<BloodAbility> */
    public function bloodAbilities() : HasMany {
        return $this->hasMany(BloodAbility::class);
    }

    /** @return HasMany<Stat> */
    public function stats() : HasMany {
        return $this->hasMany(Stat::class);
    }
}
