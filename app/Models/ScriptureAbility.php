<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScriptureAbility extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "level",
        "cost"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function scripture() : BelongsToMany {
        return $this->belongsToMany(Scripture::class);
    }
}
