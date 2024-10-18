<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodAbility extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description"
    ];

    /** @return BelongsTo<Blood, BloodAbility> */
    public function blood() : BelongsTo {
        return $this->belongsTo(Blood::class);
    }

    /** @return BelongsTo<Strategy, BloodAbility> */
    public function strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
