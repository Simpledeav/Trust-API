<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Models\Admin;
use App\Helpers\FileHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\ExpectationFailedException;
use App\Http\Controllers\NotificationController;
use App\DataTransferObjects\Models\UserModelData;

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
            // 'city_id' => $userModelData->getCityId() ?? $user->city_id,
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

    public function storeKycInfo(User $user, UserModelData $userModelData): User
    {

        // Handle file uploads for front_id and back_id
        if($userModelData->getFrontId())
            $frontIdPath = $this->uploadFile($userModelData->getFrontId(), 'kyc/front_ids');

        if($userModelData->getBackId())
            $backIdPath = $this->uploadFile($userModelData->getBackId(), 'kyc/back_ids');

        // Update the user's KYC information
        $user->update([
            'id_type' => $userModelData->getIdtype() ?? $user->id_type,
            'id_number' => $userModelData->getIdNumber() ?? $user->id_number,
            'front_id' => $userModelData->getFrontId() ? $frontIdPath : $user->front_id,
            'back_id' => $userModelData->getBackId() ? $backIdPath : $user->back_id,
            'kyc' => 'submitted',
        ]);

        $admin = Admin::where('email', config('app.admin_mail'))->first();

        NotificationController::sendAdminUserUploadIdNotification($admin, $user);

        return $user->refresh();
    }

    /**
     * Helper function to upload a file and return its storage path.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    private function uploadFile(UploadedFile $file, string $directory): string
    {
        // // Generate a unique filename
        // $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // // Store the file in the specified directory
        // $path = $file->storeAs($directory, $filename, 'public');

        // // Return the full public URL or storage path
        // return Storage::url($path);

        $path = $file->storeAs($directory, (uniqid() . '.' . $file->extension()));

        throw_if($path === false, ExpectationFailedException::class, 'ID could not be uploaded');

        $avatar = Storage::url($path);
        $avatar = FileHelper::saveFileAndReturnPath($file);

        return $avatar;

    }

    public function destroy(User $user):bool
    {
        // Finally, delete the user
        $user->forceDelete();

        return true;
    }

}
