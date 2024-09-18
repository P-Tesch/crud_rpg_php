<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $pivot_current_cooldown
 */
class MysticEye extends Model
{
    /** @use HasFactory<\Database\Factories\MysticEyeFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "passive",
        "active",
        "cooldown",
        "pivot_current_cooldown",
        "cost"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "active_strategy",
        "passive_strategy"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("current_cooldown");
    }
}
