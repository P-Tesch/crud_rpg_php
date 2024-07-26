<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolResource;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SchoolResource::collection(School::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return School::find($id);
    }

    public static function findByNameAndLevel(string $name, int $level) {
        return School::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {

    }
}
