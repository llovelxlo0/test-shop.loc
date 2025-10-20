<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\TwoFactorService;

class TwoFactorLoginController extends Controller
{
    protected TwoFactorService $service;

    public function __construct(TwoFactorService $service)
    {
        $this->service = $service;
    }
    public function showVerifyForm()
    {
        return view('TwoFactorLogin');
    }
    
    public function verify(Request $request)
    {
        
        $user = User::find(session('2fa_pending'));

    if (!$user) {
        return redirect()->route('login')->with('status', 'Session expired. Please login again.');
    }

    if ($this->service->verifyCode($user, $request->input('otp'))) {
        session()->forget('2fa_pending');
        session()->put('2fa_passed', true);
        
        Auth::login($user);

        return redirect()->intended('/')->with('status', 'Login successful!');
    }

    return back()->withErrors(['otp' => 'Неверный код']);
    }
}
