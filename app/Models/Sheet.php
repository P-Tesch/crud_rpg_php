<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        "organization"
    ];

    /** @return HasMany<Stat> */
    public function stats() : HasMany {
        return $this->hasMany(Stat::class);
    }

    /** @return HasMany<Skill> */
    public function skills() : HasMany {
        return $this->hasMany(Skill::class);
    }

    /** @return HasMany<RpgAttribute> */
    public function rpgAttributes() : HasMany {
        return $this->hasMany(RpgAttribute::class);
    }

    /** @return HasOne<Blood> */
    public function blood() : HasOne {
        return $this->hasOne(Blood::class);
    }

    /** @return BelongsToMany<Advantage> */
    public function advantages() : BelongsToMany {
        return $this->belongsToMany(Advantage::class);
    }

    /** @return HasMany<Item> */
    public function items() : HasMany {
        return $this->hasMany(Item::class);
    }

    /** @return BelongsToMany<MysticEye> */
    public function mysticEyes() : BelongsToMany {
        return $this->belongsToMany(MysticEye::class)->withPivot("current_cooldown");
    }

    /** @return BelongsToMany<School> */
    public function schools() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }

    /** @return HasMany<Effect> */
    public function effects() : HasMany {
        return $this->hasMany(Effect::class);
    }

    /** @return BelongsToMany<Miracle> */
    public function miracles() : BelongsToMany {
        return $this->belongsToMany(Miracle::class);
    }

    /** @return HasOne<Scripture> */
    public function scripture() : HasOne {
        return $this->hasOne(Scripture::class);
    }

    /** @return BelongsToMany<Sonata> */
    public function sonatas() : BelongsToMany {
        return $this->belongsToMany(Sonata::class);
    }

    /** @return BelongsToMany<SonataAbility> */
    public function sonataAbilities() : BelongsToMany {
        return $this->belongsToMany(SonataAbility::class);
    }

    /** @return BelongsToMany<System> */
    public function Systems() : BelongsToMany {
        return $this->belongsToMany(System::class);
    }

    /** @return BelongsToMany<Subsystem> */
    public function Subsystems() : BelongsToMany {
        return $this->belongsToMany(Subsystem::class);
    }
}
