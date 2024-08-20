<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SonataAbility extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "level",
    ];

    protected $hidden = [
        "strategy"
    ];

    public function sonata() : BelongsTo {
        return $this->belongsTo(Sonata::class);
    }
}
