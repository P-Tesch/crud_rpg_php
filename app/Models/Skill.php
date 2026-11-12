<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSkill
 */
class Skill extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "key",
        "value",
        "referenced_stat",
    ];

    /**
     * @return BelongsTo<Sheet, $this>
     */
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class);
    }
}
