<?php

namespace App\Services;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use App\Models\TwoFactor;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }
    public function generateSecret(User $user): string
    {
        $secret = $this->google2fa->generateSecretKey();
        $user->twoFactor()->updateOrCreate(
            ['user_id' => $user->id],
            ['secret' => $secret, 'enabled' => false]
        );
        return $secret;
    }
    public function verifySetupCode(User $user, string $code)
    {
        $twoFactor = $user->twoFactor;
        if (!$twoFactor) {
            return false;
        }
        return $this->google2fa->verifyKey($twoFactor->secret, $code);
    }
    public function verifyCode(User $user, string $code): bool
    {
        $twoFactor = $user->twoFactor;
        if (!$twoFactor || !$twoFactor->enabled) {
            return false;
        }
        return $this->google2fa->verifyKey($twoFactor->secret, $code);
    }
    public function enable(User $user)
    {
        $user->twoFactor->update(['enabled' => true]);
    }
    public function disable(User $user, string $code)
    {
        if (!$this->verifyCode($user, $code)) {
            return false;
        }
        $user->twoFactor->update([
            'enabled' => false,
            'secret' => null,
        ]);
        return true;
    }
    public function isEnabled(User $user): bool
    {
        return $user->twoFactor && $user->twoFactor->enabled;
    }
}

