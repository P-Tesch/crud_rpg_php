<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MysticEye extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "passive",
        "active",
        "cooldown",
        "pivot_current_cooldown",
        "cost"
    ];

    protected $hidden = [
        "active_strategy",
        "passive_strategy"
    ];

    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("current_cooldown");
    }
}
