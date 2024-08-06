<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Scripture extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "creation_points",
        "remaining_scripture_points",
        "damage",
        "range",
        "sharpness",
        "double",
        "description"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function scriptureAbilities() : BelongsToMany {
        return $this->belongsToMany(ScriptureAbility::class);
    }

    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }
}
