<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperSheet
 */
class Sheet extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "portrait",
        "description",
        "background",
        "creation_points",
        "alignment",
        "organization",
    ];

    /**
     * @return HasMany<Stat, $this>
     */
    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }

    /**
     * @return HasMany<Skill, $this>
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /**
     * @return HasMany<RpgAttribute, $this>
     */
    public function rpgAttributes(): HasMany
    {
        return $this->hasMany(RpgAttribute::class);
    }

    /**
     * @return HasOne<Blood, $this>
     */
    public function blood(): HasOne
    {
        return $this->hasOne(Blood::class);
    }

    /**
     * @return BelongsToMany<Advantage, $this>
     */
    public function advantages(): BelongsToMany
    {
        return $this->belongsToMany(Advantage::class);
    }

    /**
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * @return BelongsToMany<MysticEye, $this>
     */
    public function mysticEyes(): BelongsToMany
    {
        return $this->belongsToMany(MysticEye::class)->withPivot("current_cooldown");
    }

    /**
     * @return BelongsToMany<School, $this>
     */
    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class);
    }

    /**
     * @return HasMany<Effect, $this>
     */
    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

    /**
     *  @return BelongsToMany<Miracle, $this>
     */
    public function miracles(): BelongsToMany
    {
        return $this->belongsToMany(Miracle::class);
    }

    /**
     * @return HasOne<Scripture, $this>
     */
    public function scripture(): HasOne
    {
        return $this->hasOne(Scripture::class);
    }

    /**
     * @return BelongsToMany<Sonata, $this>
     */
    public function sonatas(): BelongsToMany
    {
        return $this->belongsToMany(Sonata::class);
    }

    /**
     * @return BelongsToMany<SonataAbility, $this>
     */
    public function sonataAbilities(): BelongsToMany
    {
        return $this->belongsToMany(SonataAbility::class);
    }

    /**
     * @return BelongsToMany<System, $this>
     */
    public function Systems(): BelongsToMany
    {
        return $this->belongsToMany(System::class);
    }

    /**
     * @return BelongsToMany<Subsystem, $this>
     */
    public function Subsystems(): BelongsToMany
    {
        return $this->belongsToMany(Subsystem::class);
    }
}
