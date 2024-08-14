<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "key",
        "value"
    ];

    public function blood() : BelongsTo {
        return $this->belongsTo(Blood::class);
    }

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
