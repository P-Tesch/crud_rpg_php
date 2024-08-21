<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subsystem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description"
    ];

    protected $hidden = [
        "strategy",
        "strategy_burn"
    ];

    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("burn_duration");
    }

    public function subsystems() : BelongsToMany {
        return $this->belongsToMany(Subsystem::class);
    }
}
