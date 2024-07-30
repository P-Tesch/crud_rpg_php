<?php

namespace App\Http\Controllers;

use App\Http\Resources\MysticEyeResource;
use App\Models\MysticEye;
use Illuminate\Http\Request;

class MysticEyesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MysticEyeResource::collection(MysticEye::all());
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
        return MysticEye::find($id);
    }

    public static function findByName(string $name) {
        return MysticEye::where("name", $name)->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MysticEye $mysticEye)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MysticEye $mysticEye)
    {
        //
    }
}
