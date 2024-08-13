<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller {

    public function login(Request $request) {
        return Inertia::render("vue/login", []);
    }

    public function sheet(Request $request, SheetController $sheetController) {
        if (!Auth::check()) {
            return redirect("/login");
        }

        return Inertia::render("vue/sheet", ["sheet" => $sheetController->showAsEntity($request)]);
    }
}
