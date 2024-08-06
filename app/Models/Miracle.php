<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Miracle extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "description",
        "cost"
    ];

    protected $hidden = [
        "strategy"
    ];

    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }
}
