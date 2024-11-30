<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    /** @var list<string> */
    protected $with = ["active_strategy", "passive_strategy"];

    /** @var list<string> */
    protected $fillable = [
        "name",
        "passive",
        "active",
        "cooldown",
        "cost",
        "pivot_current_cooldown",
        "active_strategy_id",
        "passive_strategy_id"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("current_cooldown");
    }

    /** @return BelongsTo<Strategy, Spell> */
    public function passive_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }

    /** @return BelongsTo<Strategy, Spell> */
    public function active_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
