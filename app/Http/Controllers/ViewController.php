<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ViewController extends Controller {

    public function login(Request $request) : Response {
        return Inertia::render("vue/login", []);
    }

    public function sheet(Request $request, SheetController $sheetController) : Response | RedirectResponse | Redirector {
        if (!Auth::check()) {
            return redirect("/login");
        }

        try {
            $sheet = $sheetController->showAsEntity($request);
            return Inertia::render("vue/sheet", ["sheet" => $sheet]);

        } catch (NotFoundResourceException $exception) {
            return redirect("/login");
        }
    }
}
