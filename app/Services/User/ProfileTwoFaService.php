<?php

namespace App\Services\User;

use App\Events\TwoFaStatusUpdated;
use App\Contracts\Auth\MustSatisfyTwoFa;

class ProfileTwoFaService
{
    /**
     * Toggle profile two-fa status.
     *
     * @param \App\Contracts\Auth\MustSatisfyTwoFa $user
     * @return bool
     */
    public function toggle(MustSatisfyTwoFa $user): bool
    {
        activity()->disableLogging();

        $user->toggleTwoFaActivation();

        $status = (bool) $user->two_fa_activated_at;

        if ($user instanceof \Illuminate\Foundation\Auth\User) {
            event(new TwoFaStatusUpdated($user));
        }

        return $status;
    }
}
