<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller {

    /**
     * Authenticates a login attempt and redirects to corresponding page
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request) : RedirectResponse {
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

    /**
     * Logs out the user and redirects to the login page
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request) : RedirectResponse {
        Auth::logout();
        return redirect("/login");
    }
}
