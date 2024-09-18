<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Subsystem;
use Illuminate\Http\Request;
use App\Http\Resources\SubsystemResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubsystemController extends Controller
{
    /**
     * List all subsystems
     * @return AnonymousResourceCollection<int, SubsystemResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return SubsystemResource::collection(Subsystem::all());
    }

    /**
     * List all subsystems filtered by related system
     * @param Request $request
     * @return AnonymousResourceCollection<int, SubsystemResource>
     */
    public function indexBySystem(Request $request) : AnonymousResourceCollection {
        $systemName = $request->input("system_name");
        return SubsystemResource::collection(Subsystem::whereBelongsTo(System::where("name", $systemName)->firstOrFail(), "system")->get());
    }

    /**
     * Persist a new subsystem
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a subsystem by id
     * @return ?Subsystem
     */
    public static function show(int $id) : ?Subsystem
    {
        return Subsystem::find($id);
    }

    /**
     * Show a subsystem by name
     * @param string $name
     * @return Subsystem
     */
    public static function findByName(string $name) : Subsystem {
        return Subsystem::where("name", $name)->firstOrFail();
    }

    /**
     * Update a subsystem
     * @return void
     */
    public function update(Request $request, Subsystem $subsystem) : void
    {
        // TODO
    }

    /**
     * Delete a subsystem
     * @return void
     */
    public function destroy(Subsystem $subsystem) : void
    {
        // TODO
    }
}
