<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scripture extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "creation_points",
        "remaining_scripture_points",
        "damage",
        "range",
        "sharpness",
        "double",
    ];

    protected $hidden = [
        "strategy"
    ];

    public function scriptureAbilities() : HasMany {
        return $this->hasMany(ScriptureAbility::class);
    }

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
