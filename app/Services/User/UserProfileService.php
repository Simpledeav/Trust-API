<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExpectationFailedException;
use App\DataTransferObjects\Models\UserModelData;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;

class UserProfileService
{
    /**
     * Update the user profile.
     *
     * @param \App\Models\User $user
     * @param \App\DataTransferObjects\Models\UserModelData $userModelData
     * @return \App\Models\User
     */
    public function update(User $user, UserModelData $userModelData): User
    {
        // Upload new avatar
        $avatar = $user->avatar;

        if ($userModelData->getAvatar() instanceof \Illuminate\Http\UploadedFile) {
            $path = $userModelData->getAvatar()
                ->storeAs('avatars', ($user->id . '.' . $userModelData->getAvatar()->extension()));

            throw_if($path === false, ExpectationFailedException::class, 'Avatar could not be uploaded');

            $avatar = Storage::url($path);
            $avatar = FileHelper::saveFileAndReturnPath($userModelData->getAvatar());
        }

        // Update profile
        $data = [
            'country_id' => $userModelData->getCountryId() ?? $user->country_id,
            'state_id' => $userModelData->getStateId() ?? $user->state_id,
            'city_id' => $userModelData->getCityId() ?? $user->city_id,
            'first_name' => $userModelData->getFirstname() ?? $user->first_name,
            'last_name' => $userModelData->getLastname() ?? $user->last_name,
            'username' => $userModelData->getUsername() ?? $user->username,
            'phone' => $userModelData->getPhoneNumber() ?? $user->phone,
            'address' => $userModelData->getAddress() ?? $user->address,
            'zipcode' => $userModelData->getZipcode() ?? $user->zipcode,
            'ssn' => $userModelData->getSsn() ?? $user->ssn,
            'dob' => $userModelData->getDateOfBirth() ?? $user->dob,
            'nationality' => $userModelData->getNationality() ?? $user->nationality,
            'currency_id' => $userModelData->getCurrencyId() ?? $user->currency_id,
            'email' => $userModelData->getEmail() ?? $user->email,
            'email_verified_at' => $user->isDirty('email') ? null : $user->email_verified_at,
            'avatar' => $avatar,
        ];
        $user->updateOrFail([...$data, ...[
            'email_verified_at' => $data['email'] !== $user->email ? null : $user->email_verified_at,
        ]]);

        return $user->withoutRelations()->refresh();
    }

    /**
     * Delete user account.
     *
     * @param \App\Models\User $user
     * @param string $reason
     * @return void
     */
    public function delete(User $user, string $reason): void
    {
        DB::transaction(fn () => $user->delete($reason));
    }

    private function saveFileAndReturnPath(\Illuminate\Http\UploadedFile $file, string $filename = null, string $path = 'assets/img'): string
    {
        $filename = $filename ?? time() . rand(1111, 9999) .'.'. $file->getClientOriginalExtension();
        $file->move($path, $filename);

        return $path . '/' . $filename;
    }
}
