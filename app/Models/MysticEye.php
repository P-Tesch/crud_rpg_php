<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MysticEye extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "passive",
        "active",
        "cooldown"
    ];

    protected $hidden = [
        "active_strategy",
        "passive_strategy"
    ];

    public function sheet() : HasOne {
        return $this->hasOne(Sheet::class);
    }
}
