<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "damage"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function effects() : HasMany {
        return $this->hasMany(Effect::class);
    }

    public function sheet() : HasOne {
        return $this->hasOne(Sheet::class);
    }
}
