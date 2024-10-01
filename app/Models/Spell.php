<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spell extends Model
{
    /** @use HasFactory<\Database\Factories\SpellFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "type",
        "strategy"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<School> */
    public function schools() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }
}
