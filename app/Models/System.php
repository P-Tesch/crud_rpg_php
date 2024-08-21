<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class System extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "damage"
    ];

    protected $hidden = [
        "strategy_passive",
        "strategy_active",
        "strategy_burn"
    ];

    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }

    public function subsystems() : HasMany {
        return $this->hasMany(Subsystem::class);
    }
}
