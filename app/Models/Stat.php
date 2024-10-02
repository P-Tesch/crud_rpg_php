<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "key",
        "value"
    ];

    /** @return BelongsTo<Blood, Stat> */
    public function blood() : BelongsTo {
        return $this->belongsTo(Blood::class);
    }

    /** @return BelongsTo<Sheet, Stat> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
