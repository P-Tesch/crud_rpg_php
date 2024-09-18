<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScriptureAbility extends Model
{
    /** @use HasFactory<\Database\Factories\ScriptureAbilityFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var array<int, string> */
    protected $fillable = [
        "name",
        "description",
        "level",
        "cost"
    ];

    /** @var array<int, string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<Scripture> */
    public function scripture() : BelongsToMany {
        return $this->belongsToMany(Scripture::class);
    }
}
