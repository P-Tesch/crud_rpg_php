<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewController;

Route::redirect("/", "/login");

Route::get('/login', [ViewController::class, "login"]);

Route::group(["middleware" => ["web"]], function () {
    Route::get("/sheet", [ViewController::class, "sheet"]);
});
