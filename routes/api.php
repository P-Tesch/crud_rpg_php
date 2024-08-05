<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RollController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\MysticEyesController;
use App\Http\Controllers\AdvantagesController;
use Illuminate\Support\Facades\Route;

Route::post("/users", [UserController::class, "store"]);
Route::post("/sheets", [SheetController::class, "store"]);

Route::group(["middleware" => ["web"]], function () {
    Route::get("/login", [LoginController::class, "authenticate"]);

    Route::get("/users", [UserController::class, "index"]);
    Route::delete("/users/{id}", [UserController::class, "destroy"]);
    Route::get("/users/{id}", [UserController::class, "show"]);

    Route::get("/sheets", [SheetController::class, "showEntityAsJson"]);
    Route::put("/sheets", [SheetController::class, "update"]);
    Route::delete("/sheets/{id}", [SheetController::class, "destroy"]);

    Route::get("/roll/generic", [RollController::class, "rollGeneric"]);
    Route::get("/roll/skill", [RollController::class, "rollSkill"]);
    Route::get("/roll/spell", [RollController::class, "rollSpell"]);
    Route::get("/roll/item", [RollController::class,"rollItem"]);
    Route::get("/roll", [RollController::class, "index"]);

    Route::get("/schools", [SchoolController::class, "index"]);

    Route::get("/mystic_eyes", [MysticEyesController::class, "index"]);

    Route::get("/advantages", [AdvantagesController::class, "index"]);
});
