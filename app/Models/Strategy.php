<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lua code that defines how an action (spell, item, mystic eye.
 *
 * ..) affects sheets.
 *
 * Each owner has its own row; strategies are not shared between records.
 *
 * @mixin IdeHelperStrategy
 */
class Strategy extends Model
{
    /** @use HasFactory<\Database\Factories\StrategyFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "code",
    ];
}
