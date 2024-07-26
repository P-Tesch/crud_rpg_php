<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class LoginController extends Controller {

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'login' => ['required'],
            'password' => ['required'],
        ]);
        $rememberMe = $request->get("remember_me");

        if (Auth::attempt($credentials, $rememberMe)) {
            $request->session()->regenerate();

            return redirect("/sheet");
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }
}
