<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Strategy extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "value"
    ];

    /** @return HasOne<Spell, Strategy> */
    public function Spell() : HasOne {
        return $this->hasOne(Spell::class);
    }

    /** @return HasOne<Effect, Strategy> */
    public function Effect() : HasOne {
        return $this->hasOne(Effect::class);
    }
}
