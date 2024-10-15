<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Strategy extends Model
{
    use HasFactory;

    protected $fillable = [
        "value"
    ];

    /** @return BelongsTo<Spell, Strategy> */
    public function Spell() : BelongsTo {
        return $this->belongsTo(Spell::class);
    }

    /** @return BelongsTo<Effect, Strategy> */
    public function Effect() : BelongsTo {
        return $this->belongsTo(Effect::class);
    }
}
