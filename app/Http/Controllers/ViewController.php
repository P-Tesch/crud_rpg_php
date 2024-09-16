<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class ViewController extends Controller {

    public function login(Request $request) : Response {
        return Inertia::render("vue/login", []);
    }

    public function sheet(Request $request, SheetController $sheetController) : Response | RedirectResponse | Redirector {
        if (!Auth::check()) {
            return redirect("/login");
        }

        return Inertia::render("vue/sheet", ["sheet" => $sheetController->showAsEntity($request)]);
    }
}
