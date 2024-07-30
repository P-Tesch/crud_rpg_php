<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "level",
        "cost"
    ];

    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }

    public function spells() : BelongsToMany {
        return $this->belongsToMany(Spell::class);
    }
}
