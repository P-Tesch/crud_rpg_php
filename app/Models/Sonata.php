<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sonata extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description"
    ];

    public function sonataAbilities() : HasMany {
        return $this->hasMany(SonataAbility::class);
    }

    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }
}
