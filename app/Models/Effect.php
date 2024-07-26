<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Effect extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "remaining_turns"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function item() : BelongsTo {
        return $this->belongsTo(Item::class);
    }

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
