<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\TwoFactorService;
use PragmaRX\Google2FAQRCode\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProfileController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }
    public function showProfile() {
        $user = Auth::user();
        $isTwoFactorEnabled = $user->twoFactor && $user->twoFactor->enabled;
        $secret = session('2fa_secret');
        $qrCodeUrl = session('2fa_qrCodeUrl');

        return view('Profile', [
            'user' => $user,
            'isTwoFactorEnabled' => $isTwoFactorEnabled,
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret,
        ]); 
    }

    public function editProfile(Request $request) {
        
        $user = User::find(Auth::id()); 
        $rules = []; 

        if ($request->filled('name')) {
            $rules['name'] = 'string|max:255';  
        }
        if ($request->filled('email')) {
            $rules['email'] = 'string|email|max:255|unique:users,email,' . $user->id;  
        }
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }
        $validated = $request->validate($rules);

        // update only filled fields
        if (isset($validated['name'])) {  
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {  // Update email
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function setupTwoFactor() 
    {
        $user = Auth::user();
        $secret = $this->twoFactorService->generateSecret($user);
        $google2fa = new Google2FA();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->name,
            $secret
        );
        session([
            '2fa_secret' => $secret,
            '2fa_qrCodeUrl' => $qrCodeUrl,
        ]);
        return redirect()->route('profile');
    }

    public function verify(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'otp' => 'required|string',
        ]);
        if ($this->twoFactorService->verifyCode($user, $request->input('otp'))) {
            $this->twoFactorService->enable($user);
            session()->forget(['2fa_secret', '2fa_qrCodeUrl']);
            return redirect()->route('profile')->with('success', 'Two-Factor Authentication enabled successfully!');
        }
        return redirect()->route('profile')->withErrors(['otp' => 'Invalid verification code.']);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $this->twoFactorService->disable($user);
        session()->forget(['2fa_secret', '2fa_qrCodeUrl']);
        return redirect()->route('profile')->with('success', 'Two-Factor Authentication disabled successfully!');
    }
}