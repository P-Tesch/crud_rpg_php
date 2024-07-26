<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Advantage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "level"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
