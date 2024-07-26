<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodAbility extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function blood() : BelongsTo {
        return $this->belongsTo(Blood::class);
    }
}
