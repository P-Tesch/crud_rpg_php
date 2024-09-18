<?php

namespace App\Http\Controllers;

use App\Http\Resources\MysticEyeResource;
use App\Models\MysticEye;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MysticEyesController extends Controller
{
    /**
     * List all mystic eyes
     * @return AnonymousResourceCollection<int, MysticEye>
     */
    public function index() : AnonymousResourceCollection
    {
        return MysticEyeResource::collection(MysticEye::all());
    }

    /**
     * Persist a new mystic eye
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a mystic eye by id
     * @return ?MysticEye
     */
    public function show(int $id) : ?MysticEye
    {
        return MysticEye::find($id);
    }

    /**
     * Show a mystic eye by name
     * @param string $name
     * @return MysticEye
     */
    public static function findByName(string $name) : MysticEye {
        return MysticEye::where("name", $name)->firstOrFail();
    }

    /**
     * Update a mystic eye
     * @return void
     */
    public function update(Request $request, MysticEye $mysticEye) : void
    {
        // TODO
    }

    /**
     * Delete a mystic eye
     * @return void
     */
    public function destroy(MysticEye $mysticEye) : void
    {
        // TODO
    }
}
