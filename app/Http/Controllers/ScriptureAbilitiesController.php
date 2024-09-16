<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScriptureAbilityResource;
use App\Models\ScriptureAbility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScriptureAbilitiesController extends Controller
{
    /**
     * List all scripture abilities
     * @return AnonymousResourceCollection<int, ScriptureAbilityResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return ScriptureAbilityResource::collection(ScriptureAbility::all());
    }

    /**
     * Persist a new scripture ability
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a scripture ability by id
     * @return ?ScriptureAbility
     */
    public function show(int $id) : ?ScriptureAbility
    {
        return ScriptureAbility::find($id);
    }

    /**
     * Show a scripture ability by name and level
     * @param string $name
     * @param int $level
     * @return ScriptureAbility
     */
    public static function findByNameAndLevel(string $name, int $level) : ScriptureAbility {
        return ScriptureAbility::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update a scripture ability
     * @return void
     */
    public function update(Request $request, ScriptureAbility $advantage) : void
    {
        // TODO
    }

    /**
     * Delete a scripture ability
     * @return void
     */
    public function destroy(ScriptureAbility $advantage) : void
    {
        // TODO
    }
}
