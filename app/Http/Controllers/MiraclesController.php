<?php

namespace App\Http\Controllers;

use App\Http\Resources\MiracleResource;
use App\Models\Advantage;
use App\Models\Miracle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MiraclesController extends Controller
{
    /**
     * List all miracles
     * @return AnonymousResourceCollection<int, MiracleResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return MiracleResource::collection(Miracle::all());
    }

    /**
     * Persist a new miracle
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a miracle by id
     * @return ?Miracle
     */
    public function show(int $id) : ?Miracle
    {
        return Miracle::find($id);
    }

    /**
     * Show a miracle by name
     * @param string $name
     * @return Miracle
     */
    public static function findByName(string $name) : Miracle {
        return Miracle::where("name", $name)->firstOrFail();
    }

    /**
     * Update the miracle
     * @return void
     */
    public function update(Request $request, Advantage $advantage) : void
    {
        // TODO
    }

    /**
     * Delete a miracle
     * @return void
     */
    public function destroy(Advantage $advantage) : void
    {
        // TODO
    }
}
