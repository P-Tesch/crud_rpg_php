<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sheet extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "portrait",
        "description",
        "background",
        "creation_points",
        "alignment",
        "organization"
    ];

    public function stats() : HasMany {
        return $this->hasMany(Stat::class);
    }

    public function skills() : HasMany {
        return $this->hasMany(Skill::class);
    }

    public function rpgAttributes() : HasMany {
        return $this->hasMany(RpgAttribute::class);
    }

    public function blood() : HasOne {
        return $this->hasOne(Blood::class);
    }

    public function advantages() : BelongsToMany {
        return $this->belongsToMany(Advantage::class);
    }

    public function items() : HasMany {
        return $this->hasMany(Item::class);
    }

    public function mysticEyes() : BelongsToMany {
        return $this->belongsToMany(MysticEye::class)->withPivot("current_cooldown");
    }

    public function schools() : BelongsToMany {
        return $this->belongsToMany(School::class);
    }

    public function effects() : HasMany {
        return $this->hasMany(Effect::class);
    }

    public function miracles() : BelongsToMany {
        return $this->belongsToMany(Miracle::class);
    }

    public function scripture() : HasOne {
        return $this->hasOne(Scripture::class);
    }

    public function sonatas() : BelongsToMany {
        return $this->belongsToMany(Sonata::class);
    }

    public function sonataAbilities() : BelongsToMany {
        return $this->belongsToMany(SonataAbility::class);
    }

    public function systems() : BelongsToMany {
        return $this->belongsToMany(System::class);
    }

    public function subsystems() : BelongsToMany {
        return $this->belongsToMany(Subsystem::class);
    }
}
