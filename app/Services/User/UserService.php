<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Country;
use App\Models\SystemData;
use Illuminate\Support\Str;
use App\Models\UserSettings;
use App\Enums\SystemDataCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\NotificationController;
use App\DataTransferObjects\Models\UserModelData;
use App\DataTransferObjects\Auth\AuthenticationCredentials;

class UserService
{
    /**
     * Create a new user.
     *
     * @param \App\DataTransferObjects\Models\UserModelData $userModelData
     * @param bool $authenticate
     * @return \App\Models\User|\App\DataTransferObjects\Auth\AuthenticationCredentials
     */
    public function create(UserModelData $userModelData, bool $authenticate = false): User|AuthenticationCredentials
    {
        return DB::transaction(function () use ($userModelData, $authenticate) {
            try {
                /** @var \App\Models\User $user */
                $user = User::query()->create([
                    'first_name' => $userModelData->getFirstname(),
                    'last_name' => $userModelData->getLastname(),
                    'email' => $userModelData->getEmail(),
                    'password' => $userModelData->getPassword() ? Hash::make($userModelData->getPassword()) : null,
                    'username' => $userModelData->getUsername(),
                    'phone' => $userModelData->getPhoneNumber(),
                    'address' => $userModelData->getAddress(),
                    'zipcode' => $userModelData->getZipcode(),
                    'ssn' => $userModelData->getSsn(),
                    'dob' => $userModelData->getDateOfBirth(),
                    'nationality' => $userModelData->getNationality(),
                    'experience' => $userModelData->getExperience(),
                    'employed' => $userModelData->getEmployed(),
                    'status' => 'active',
                    'kyc' => 'pending',
                    'id_number' => $userModelData->getIdNumber(),
                    'front_id' => $userModelData->getFrontId(),
                    'back_id' => $userModelData->getBackId(),
                    'country_id' => $userModelData->getCountryId(),
                    'state_id' => $userModelData->getStateId(),
                    'city' => $userModelData->getCity(),
                    'currency_id' => $userModelData->getCurrencyId(),
                ])->refresh();
    
                event(new Registered($user));
    
                Wallet::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);
    
                UserSettings::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id
                ]);
    
                $admin = Admin::where('email', config('app.admin_mail'))->first();
    
                NotificationController::sendAdminNewUserNotification($admin, $user);
    
                return $authenticate
                    ? (new AuthenticationCredentials())
                        ->setUser($user)
                        ->setApiMessage('User created successfully')
                        ->setToken($user->createToken($user->getMorphClass())->plainTextToken)
                    : $user;
    
            } catch (\Throwable $e) {
                // Optional: log error
                throw new \Exception("User creation failed: " . $e->getMessage(), 0, $e);
            }
        });
    }
    
}
