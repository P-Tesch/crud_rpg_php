<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spell extends Model
{
    /** @use HasFactory<\Database\Factories\SpellFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "type"
    ];

    /** @return BelongsToMany<School> */
    public function schools() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }

    /** @return HasOne<Strategy> */
    public function strategy() : HasOne {
        return $this->hasOne(Strategy::class);
    }
}
