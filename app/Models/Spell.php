<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spell extends Model
{
    /** @use HasFactory<\Database\Factories\SpellFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $with = ["strategy"];

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "type",
        "strategy_id"
    ];

    /** @return BelongsToMany<School> */
    public function schools() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }

    /** @return BelongsTo<Strategy> */
    public function strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
