<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdvantageResource;
use App\Models\Advantage;
use Illuminate\Http\Request;

class AdvantagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AdvantageResource::collection(Advantage::all());
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
        return Advantage::find($id);
    }

    public static function findByNameAndLevel(string $name, int $level) {
        return Advantage::where("name", $name)->where("level", $level)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Advantage $advantage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Advantage $advantage)
    {
        //
    }
}
