<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperAdvantage
 */
class Advantage extends Model
{
    /** @use HasFactory<\Database\Factories\AdvantageFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "level",
        "cost",
        "class",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy",
    ];

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheet(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class);
    }
}
