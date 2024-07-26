<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blood extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "impulses"
    ];

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }

    public function bloodAbilities() : HasMany {
        return $this->hasMany(BloodAbility::class);
    }

    public function stats() : HasMany {
        return $this->hasMany(Stat::class);
    }
}
