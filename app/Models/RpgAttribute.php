<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RpgAttribute extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var ?string */
    protected $table = "attributes";

    /** @var list<string> */
    protected $fillable = [
        "key",
        "value"
    ];

    /** @return BelongsTo<Sheet, RpgAttribute> */
    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
