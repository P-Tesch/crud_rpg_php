<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Subsystem;
use Illuminate\Http\Request;
use App\Http\Resources\SubsystemResource;

class SubsystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SubsystemResource::collection(Subsystem::all());
    }

    public function indexBySonata(Request $request) {
        $subsystemName = $request->input("subsystem_name");
        return SubsystemResource::collection(Subsystem::whereBelongsTo(System::where("name", $subsystemName)->firstOrFail(), "subsystem")->get());
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
        return Subsystem::find($id);
    }

    public static function findByNameAndLevel(string $name, int $level) {
        return Subsystem::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subsystem $subsystem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subsystem $subsystem)
    {
        //
    }
}
