<?php

namespace App\Http\Controllers;

use App\Http\Resources\SonataResource;
use App\Models\Sonata;
use Illuminate\Http\Request;

class SonatasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SonataResource::collection(Sonata::all());
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
    public static function show(int $id)
    {
        return Sonata::find($id);
    }

    public static function findByName(string $name) {
        return Sonata::where("name", $name)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sonata $sonata)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sonata $sonata)
    {
        //
    }
}
