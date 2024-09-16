<?php

namespace App\Http\Controllers;

use App\Http\Resources\SystemResource;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SystemController extends Controller
{
    /**
     * List all systems
     * @return AnonymousResourceCollection<int, SystemResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return SystemResource::collection(System::all());
    }

    /**
     * Persist a new system
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a system by id
     * @return ?System
     */
    public function show(int $id) : ?System
    {
        return System::find($id);
    }

    /**
     * Show a system by name
     * @param string $name
     * @return System
     */
    public static function findByName(string $name) : System {
        return System::where("name", $name)->firstOrFail();
    }

    /**
     * Update a system
     * @return void
     */
    public function update(Request $request, System $advantage) : void
    {
        // TODO
    }

    /**
     * Delete a system by id
     * @return void
     */
    public function destroy(System $advantage) : void
    {
        // TODO
    }
}
