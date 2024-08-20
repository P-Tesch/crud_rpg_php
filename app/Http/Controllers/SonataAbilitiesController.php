<?php

namespace App\Http\Controllers;

use App\Http\Resources\SonataAbilityResource;
use App\Models\Sonata;
use App\Models\SonataAbility;
use Illuminate\Http\Request;

class SonataAbilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SonataAbilityResource::collection(SonataAbility::all());
    }

    public function indexBySonata(Request $request) {
        $sonataName = $request->input("sonata_name");
        return SonataAbilityResource::collection(SonataAbility::whereBelongsTo(Sonata::where("name", $sonataName)->firstOrFail(), "sonata")->get());
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
    public static function show(int $id)
    {
        return SonataAbility::find($id);
    }

    public static function findByNameAndLevel(string $name, int $level) {
        return SonataAbility::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sonata $sonata)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sonata $sonata)
    {
        //
    }
}
