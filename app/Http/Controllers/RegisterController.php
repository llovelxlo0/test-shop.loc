<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showForm(): View {
        return view('Register'); //-showRegisterform from Register.blade.php
    }

    public function processForm(Request $request): RedirectResponse {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',  //-create user table from migration
            'password' => 'required|string|min:6|confirmed',
        ]);

        //dd('data are valid'); //-check data are valid

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make( $request->password,)
        ]);
        //dd($user); //-check user created

        return redirect()->route('login')->with('success', 'Registration successful!');
    }
}
