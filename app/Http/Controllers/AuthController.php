<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\CartService;
use App\Services\TwoFactorService;



class AuthController extends Controller
{
    protected TwoFactorService $twoFactorService;
    protected CartService $cartService;
    public function __construct(CartService $cartService, TwoFactorService $twoFactorService)
    {
        $this->cartService = $cartService;
        $this->twoFactorService = $twoFactorService;
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
            session()->regenerate();
            $user = Auth::user();

            if ($user->twoFactor && $user->twoFactor->enabled) {
                session(['2fa_pending' => $user->id]);
                return redirect()->route('2fa.login.form');
            }

            $this->cartService->mergeSessionCartToUser($user->id);
            return redirect('/')->with('status', 'Login successful!');
        }

        return back()->withInput()->with('status', 'Invalid credentials.');
    }
    public function logout() : RedirectResponse {
        Auth::logout();
        return redirect('/')->with('status', 'Logged out successfully.');
    }
    

}
