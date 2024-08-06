<?php

namespace App\Http\Controllers;

use App\Http\Resources\MiracleResource;
use App\Models\Advantage;
use App\Models\Miracle;
use Illuminate\Http\Request;

class MiraclesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MiracleResource::collection(Miracle::all());
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
        return Miracle::find($id);
    }

    public static function findByName(string $name) {
        return Miracle::where("name", $name)->firstOrFail();
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
