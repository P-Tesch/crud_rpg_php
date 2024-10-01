<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "damage"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<Effect> */
    public function effects() : BelongsToMany {
        return $this->belongsToMany(Effect::class);
    }

    /** @return BelongsTo<Sheet> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
