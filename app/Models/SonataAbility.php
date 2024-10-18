<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SonataAbility extends Model
{
    /** @use HasFactory<\Database\Factories\SonataAbilityFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "level",
        "cost",
        "sonata_id"
    ];


    /** @return BelongsTo<Sonata, SonataAbility> */
    public function sonata() : BelongsTo {
        return $this->belongsTo(Sonata::class);
    }

    /** @return BelongsTo<Strategy, SonataAbility> */
    public function strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
