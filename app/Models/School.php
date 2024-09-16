<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "level",
        "cost"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }

    /** @return BelongsToMany<Spell> */
    public function spells() : BelongsToMany {
        return $this->belongsToMany(Spell::class);
    }
}
