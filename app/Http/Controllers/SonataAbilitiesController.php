<?php

namespace App\Http\Controllers;

use App\Http\Resources\SonataAbilityResource;
use App\Models\Sonata;
use App\Models\SonataAbility;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SonataAbilitiesController extends Controller
{
    /**
     * List all sonata abilities
     * @return AnonymousResourceCollection<int, SonataAbilityResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return SonataAbilityResource::collection(SonataAbility::all());
    }

    /**
     * List all sonata abilities by related sonata
     * @param Request $request
     * @return AnonymousResourceCollection<int, SonataAbilityResource>
     */
    public function indexBySonata(Request $request) : AnonymousResourceCollection {
        $sonataName = $request->input("sonata_name");
        return SonataAbilityResource::collection(SonataAbility::whereBelongsTo(Sonata::where("name", $sonataName)->firstOrFail(), "sonata")->get());
    }

    /**
     * Persist a new sonata ability
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a sonata ability by id
     * @return ?SonataAbility
     */
    public static function show(int $id) : ?SonataAbility
    {
        return SonataAbility::find($id);
    }

    /**
     * Show a sonata ability by name and level
     * @param string $name
     * @param int $level
     * @return SonataAbility
     */
    public static function findByNameAndLevel(string $name, int $level) : SonataAbility {
        return SonataAbility::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update a sonata ability
     * @return void
     */
    public function update(Request $request, Sonata $sonata) : void
    {
        // TODO
    }

    /**
     * Delete a sonata ability
     * @return void
     */
    public function destroy(Sonata $sonata) : void
    {
        // TODO
    }
}
