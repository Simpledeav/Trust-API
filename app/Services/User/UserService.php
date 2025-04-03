<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Country;
use App\Models\SystemData;
use Illuminate\Support\Str;
use App\Enums\SystemDataCode;
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
        /** @var \App\Models\User $user */
        $user = User::query()->create([
            'first_name' => $userModelData->getFirstname(),  // Corrected field name
            'last_name' => $userModelData->getLastname(),  // Corrected field name
            'email' => $userModelData->getEmail(),
            'password' => $userModelData->getPassword() ? Hash::make($userModelData->getPassword()) : null,
            'username' => $userModelData->getUsername(),
            'phone' => $userModelData->getPhoneNumber(),
            'address' => $userModelData->getAddress(),  // Added new field 'address'
            'zipcode' => $userModelData->getZipcode(),  // Added new field 'zipcode'
            'ssn' => $userModelData->getSsn(),  // Added new field 'ssn'
            'dob' => $userModelData->getDateOfBirth(),
            'nationality' => $userModelData->getNationality(),  // Added new field 'nationality'
            'experience' => $userModelData->getExperience(),  // Added new field 'experience'
            'employed' => $userModelData->getEmployed(),  // Added new field 'employed'
            'status' => 'active',  // Default status
            'kyc' => 'pending',  // Default KYC status
            'id_number' => $userModelData->getIdNumber(),  // Added new field 'id_number'
            'front_id' => $userModelData->getFrontId(),  // Added new field 'front_id'
            'back_id' => $userModelData->getBackId(),  // Added new field 'back_id'
            'country_id' => $userModelData->getCountryId(),
            'state_id' => $userModelData->getStateId(),
            'city' => $userModelData->getCity(),
            'currency_id' => $userModelData->getCurrencyId(),
        ])->refresh();

        // Fire the Registered event
        event(new Registered($user));

        // **Create wallet for the user**
        Wallet::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'balance' => 0, // Default balance
        ]);

        // Create payment records for the user
        $user->storePayment('admin', []);
        $user->storePayment('user', []);

        $admin = Admin::where('email', config('app.admin_mail'))->first();

        NotificationController::sendAdminNewUserNotification($admin, $user);

        // Return user or authentication credentials
        return $authenticate
            ? (new AuthenticationCredentials())
                ->setUser($user)
                ->setApiMessage('User created successfully')
                ->setToken($user->createToken($user->getMorphClass())->plainTextToken)
            : $user;
    }
}
