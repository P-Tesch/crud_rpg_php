<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvantageResource;
use App\Models\Advantage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdvantagesController extends Controller
{
    /**
     * Lists all advantages
     * @return AnonymousResourceCollection<int, AdvantageResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return AdvantageResource::collection(Advantage::all());
    }

    /**
     * Persists a new advantage
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a advantage by id
     * @return ?Advantage
     */
    public function show(int $id) : ?Advantage
    {
        return Advantage::find($id);
    }

    /**
     * Show a advantage by name and level
     * @param string $name
     * @param int $level
     * @return Advantage
     */
    public static function findByNameAndLevel(string $name, int $level) : Advantage {
        return Advantage::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update a advantage
     * @return void
     */
    public function update(Request $request, Advantage $advantage) : void
    {
        // TODO
    }

    /**
     * Delete a advantage
     * @return void
     */
    public function destroy(Advantage $advantage) : void
    {
        // TODO
    }
}
