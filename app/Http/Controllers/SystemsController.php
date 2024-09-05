<?php

namespace App\Http\Controllers;

use App\Http\Resources\SystemResource;
use App\Models\System;
use Illuminate\Http\Request;

class SystemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SystemResource::collection(System::all());
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
    public function show(int $id)
    {
        return System::find($id);
    }

    public static function findByName(string $name) {
        return System::where("name", $name)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, System $advantage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(System $advantage)
    {
        //
    }
}
