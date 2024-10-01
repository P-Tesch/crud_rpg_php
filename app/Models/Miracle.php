<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Miracle extends Model
{
    /** @use HasFactory<\Database\Factories\MiracleFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "cost"
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheet() : BelongsToMany {
        return $this->belongsToMany(Sheet::class);
    }
}
