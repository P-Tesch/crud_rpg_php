<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $pivot_remaining_duration
 */
class Effect extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "remaining_duration",
        "strategy"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<Item> */
    public function items() : BelongsToMany {
        return $this->belongsToMany(Item::class)->withPivot("remaining_duration");
    }

    /** @return BelongsToMany<Sheet> */
    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("remaining_duration");
    }

    /** @return BelongsToMany<System> */
    public function systems() : BelongsToMany {
        return $this->belongsToMany(System::class)->withPivot("remaining_duration");
    }
}
