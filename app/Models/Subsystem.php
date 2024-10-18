<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subsystem extends Model
{
    /** @use HasFactory<\Database\Factories\SubsystemFactory> */
    use HasFactory;

    /** @var bool */
    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        "name",
        "description",
        "system_id"
    ];

    /** @return BelongsToMany<Sheet> */
    public function sheets() : BelongsToMany {
        return $this->belongsToMany(Sheet::class)->withPivot("burn_duration");
    }

    /** @return BelongsTo<System, Subsystem> */
    public function system() : BelongsTo {
        return $this->belongsTo(System::class);
    }

    /** @return BelongsTo<Strategy, Subsystem> */
    public function passive_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }

    /** @return BelongsTo<Strategy, Subsystem> */
    public function active_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }

    /** @return BelongsTo<Strategy, Subsystem> */
    public function burn_strategy() : BelongsTo {
        return $this->belongsTo(Strategy::class);
    }
}
