<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Effect extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "remaining_turns"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function items() : BelongsToMany {
        return $this->belongsToMany(Item::class)->withPivot("remaining_duration");
    }

    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("remaining_duration");
    }

    public function systems() : BelongsToMany {
        return $this->belongsToMany(System::class)->withPivot("remaining_duration");
    }
}
