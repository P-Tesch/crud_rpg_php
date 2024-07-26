<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spell extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "type"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function school() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }
}
