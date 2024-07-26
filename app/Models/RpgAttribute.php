<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RpgAttribute extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "attributes";

    protected $fillable = [
        "key",
        "value"
    ];

    // TODO remover
    protected $hidden = [
        "strategy"
    ];

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
