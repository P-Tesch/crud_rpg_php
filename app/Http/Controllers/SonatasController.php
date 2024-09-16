<?php

namespace App\Http\Controllers;

use App\Http\Resources\SonataResource;
use App\Models\Sonata;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SonatasController extends Controller
{
    /**
     * List all sonatas
     * @return AnonymousResourceCollection<int, SonataResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return SonataResource::collection(Sonata::all());
    }

    /**
     * Persist a new sonata
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a sonata by id
     * @return ?Sonata
     */
    public static function show(int $id) : ?Sonata
    {
        return Sonata::find($id);
    }

    /**
     * Show a sonata by name
     * @param string $name
     * @return Sonata
     */
    public static function findByName(string $name) : Sonata {
        return Sonata::where("name", $name)->firstOrFail();
    }

    /**
     * Update a sonata
     * @return void
     */
    public function update(Request $request, Sonata $sonata) : void
    {
        // TODO
    }

    /**
     * Delete a sonata
     * @return void
     */
    public function destroy(Sonata $sonata) : void
    {
        // TODO
    }
}
