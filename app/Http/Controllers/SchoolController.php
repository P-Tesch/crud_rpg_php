<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SchoolController extends Controller
{
    /**
     * List all schools
     * @return AnonymousResourceCollection<int, SchoolResource>
     */
    public function index() : AnonymousResourceCollection
    {
        return SchoolResource::collection(School::all());
    }

    /**
     * Persist a new school
     * @return void
     */
    public function store(Request $request) : void
    {
        // TODO
    }

    /**
     * Show a school by id
     * @return ?School
     */
    public function show(int $id) : ?School
    {
        return School::find($id);
    }

    /**
     * Show a school by name and level
     * @param string $name
     * @param int $level
     * @return School
     */
    public static function findByNameAndLevel(string $name, int $level) : School {
        return School::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update a school
     * @return void
     */
    public function update(Request $request, User $user) : void
    {
        // TODO
    }

    /**
     * Delete a school
     * @return void
     */
    public function destroy(int $id) : void
    {
        // TODO
    }
}
