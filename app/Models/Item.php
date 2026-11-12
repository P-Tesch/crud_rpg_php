<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperItem
 */
class Item extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "damage",
    ];

    /** @var list<string> */
    protected $hidden = [
        "strategy",
    ];

    /**
     * @return BelongsToMany<Effect, $this>
     */
    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(Effect::class);
    }

    /**
     * @return BelongsTo<Sheet, $this>
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class);
    }
}
