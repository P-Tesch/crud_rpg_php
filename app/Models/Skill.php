<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "key",
        "value",
        "referenced_stat"
    ];

    public function sheet() : BelongsTo {
        return $this->belongsTo(Sheet::class);
    }
}
