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

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "damage"
    ];

    /** @return BelongsToMany<Effect> */
    public function effects() : BelongsToMany {
        return $this->belongsToMany(Effect::class);
    }

    /** @return BelongsTo<Sheet, Item> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }

    /** @return BelongsTo<Strategy, Item> */
    public function strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
