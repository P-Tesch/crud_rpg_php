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

    public function indexBySystem(Request $request) {
        $systemName = $request->input("system_name");
        return SubsystemResource::collection(Subsystem::whereBelongsTo(System::where("name", $systemName)->firstOrFail(), "system")->get());
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

    public static function findByName(string $name) {
        return Subsystem::where("name", $name)->firstOrFail();
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
