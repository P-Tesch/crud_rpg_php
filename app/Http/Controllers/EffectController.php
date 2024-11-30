<?php

namespace App\Http\Controllers;

use App\Models\Effect;
use Illuminate\Http\Request;

class EffectController extends Controller
{
    public static function findByName(string $name) : Effect {
        return Effect::where("name", "=", $name)->firstOrFail();
    }
}
