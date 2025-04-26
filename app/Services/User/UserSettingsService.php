<?php

namespace App\Services\User;

use App\Models\Setting;

class UserSettingsService
{
    protected $global;

    public function __construct()
    {
        $this->global = Setting::first(); // you can cache this if needed
    }

    public function getValue($user, $key)
    {
        $userSetting = $user->settings;

        if ($userSetting && $userSetting->$key > 0) {
            return $userSetting->$key;
        }

        return $this->global->$key ?? null;
    }
}
