<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Strategy extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "value"
    ];

    /** @return array<string, string> */
    protected function casts() : array {
        return [
            "value" => "array"
        ];
    }

    /** @return HasOne<Spell> */
    public function Spell() : HasOne {
        return $this->hasOne(Spell::class);
    }

    /** @return HasOne<Effect> */
    public function Effect() : HasOne {
        return $this->hasOne(Effect::class);
    }
}
