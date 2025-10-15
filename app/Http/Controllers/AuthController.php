<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\CartService;



class AuthController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function showLoginForm(): View {
        return view('Login');
    }

    public function processLogin(Request $request):RedirectResponse {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('name', 'password');
        if (Auth::attempt($credentials)) {
            $this->cartService->mergeSessionCartToUser(Auth::id());
            return redirect('/')->with('status', 'Login successful!');
        }
        return back()->withInput()->with('status', 'Invalid credentials.');
    }
    public function logout() : RedirectResponse {
        Auth::logout();
        return redirect('/')->with('status', 'Logged out successfully.');
    }
    
}
