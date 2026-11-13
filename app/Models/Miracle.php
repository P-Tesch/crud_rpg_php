<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperMiracle
 */
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
        "cost",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy_id",
        "strategy",
    ];

    /**
     * @return BelongsToMany<Sheet, $this>
     */
    public function sheet(): BelongsToMany
    {
        return $this->belongsToMany(Sheet::class);
    }

    /**
     * The Lua code run as the action's strategy.
     *
     * @return BelongsTo<Strategy, $this>
     */
    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }
}
