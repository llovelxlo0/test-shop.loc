<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;



class AuthController extends Controller
{
    public function showLoginForm(): View {
        return view('Login'); //-showLoginform from Login.blade.php
    }

    public function processLogin(Request $request):RedirectResponse {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        //dd('data are valid'); //-check data are valid

        $credentials = $request->only('name', 'password');
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return redirect('/')->with('status', 'Login successful!');
        }
        return back()->withInput()->with('status', 'Invalid credentials.');
    }
    public function logout() : RedirectResponse {
        Auth::logout();
        return redirect('/')->with('status', 'Logged out successfully.');
    }
}
