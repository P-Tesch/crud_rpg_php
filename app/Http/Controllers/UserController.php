<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users
     * @return Collection<int, User>
     * */
    public function index() : Collection
    {
        return User::all();
    }

    /**
     * Persist a new User
     * @return void
     */
    public function store(StoreUserRequest $request, SheetController $sheetController) : void
    {
        $sheetId = $sheetController->store($request);
        $user = new User();
        $user->login = $request->input("login");
        $user->password = $request->input("password");
        $user->sheet_id = $sheetId;
        $user->save();
    }

    /**
     * Show a user by id
     * @return ?User
     */
    public function show(int $id) : ?User
    {
        return User::find($id);
    }

    /**
     * Update a user
     * @return void
     */
    public function update(Request $request, User $user) : void
    {
        // TODO
    }

    /**
     * Delete a user by id
     * @return void
     */
    public function destroy(int $id) : void
    {
        // TODO
    }
}
