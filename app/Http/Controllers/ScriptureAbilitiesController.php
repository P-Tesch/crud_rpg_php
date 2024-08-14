<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScriptureAbilityResource;
use App\Models\ScriptureAbility;
use Illuminate\Http\Request;

class ScriptureAbilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ScriptureAbilityResource::collection(ScriptureAbility::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return ScriptureAbility::find($id);
    }

    public static function findByNameAndLevel(string $name, int $level) {
        return ScriptureAbility::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScriptureAbility $advantage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScriptureAbility $advantage)
    {
        //
    }
}
