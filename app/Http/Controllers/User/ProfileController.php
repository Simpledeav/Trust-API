<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\ProfileTwoFaService;
use Symfony\Component\HttpFoundation\Response;
use App\Services\User\ProfilePasswordService;
use App\DataTransferObjects\Models\UserModelData;
use App\Services\User\UserProfileService;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\Http\Requests\User\Profile\DeleteProfileRequest;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Http\Requests\User\UpdatePasswordRequest;

class ProfileController extends Controller
{
    /**
     * Get account data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Define allowed includes and the fields to be selected
        $allowedIncludes = [
            'country' => ['id', 'name', 'phone_code'],
            'state' => ['id', 'name'],
            'city' => ['id', 'name'],
            'transactions' => ['id', 'amount', 'status', 'type', 'user_id'],
            'wallet' => ['id', 'balance', 'user_id'],
            'savings' => ['id', 'balance', 'user_id'],
        ];

        // Get requested includes and filter only allowed ones
        $requestedIncludes = array_intersect(
            explode(',', $request->query('include', '')), 
            array_keys($allowedIncludes)
        );

        // Load requested relationships with selected fields
        if (!empty($requestedIncludes)) {
            foreach ($requestedIncludes as $relation) {
                $user->load([$relation => function ($query) use ($allowedIncludes, $relation) {
                    $query->select($allowedIncludes[$relation]);
                }]);
            }
        }

        return ResponseBuilder::asSuccess()
            ->withMessage('Account fetched successfully')
            ->withData([
                'user' => $user,
            ])
            ->build();
    }

    /**
     * Update profile password.
     *
     * @param \App\Http\Requests\User\Profile\UpdatePasswordRequest $request
     * @param \App\Services\Profile\ProfilePasswordService $profilePasswordService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePassword(
        UpdatePasswordRequest $request,
        ProfilePasswordService $profilePasswordService
    ): Response {
        $profilePasswordService->update($request->user(), $request->new_password);

        return ResponseBuilder::asSuccess()
            ->withMessage('Profile password updated successfully')
            ->build();
    }

    /**
     * Toggle Two-FA status.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\Profile\ProfileTwoFaService $profileTwoFaService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTwoFa(Request $request, ProfileTwoFaService $profileTwoFaService): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $status = $profileTwoFaService->toggle($user);

        return ResponseBuilder::asSuccess()
            ->withMessage('Two-FA status updated successfully')
            ->withData([
                'status' => $status,
            ])
            ->build();
    }

    /**
     * Update profile.
     *
     * @param \App\Http\Requests\User\ProfileUpdateRequest $request
     * @param \App\Services\Profile\User\UserProfileService $userProfileService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateProfile(ProfileUpdateRequest $request, UserProfileService $userProfileService): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user = $userProfileService->update(
            $user,
            (new UserModelData())
                ->setFirstName($request->first_name)
                ->setLastName($request->last_name)
                ->setUsername($request->username)
                ->setPhoneNumber($request->phone)
                ->setDateOfBirth($request->dob)
                ->setAddress($request->address)
                ->setZipcode($request->zipcode)
                ->setSsn($request->ssn)
                ->setNationality($request->nationality)
                ->setCountryId($request->country_id)
                ->setStateId($request->state_id)
                ->setCityId($request->city_id)
                ->setCurrencyId($request->currency_id)
                ->setAvatar($request->file('avatar')),
        );

        return ResponseBuilder::asSuccess()
            ->withMessage('Profile updated successfully')
            ->withData([
                'user' => $user,
            ])
            ->build();
    }

    // /**
    //  * Delete profile.
    //  *
    //  * @param \App\Http\Requests\User\Profile\DeleteProfileRequest $request
    //  * @param \App\Services\Profile\User\UserProfileService $userProfileService
    //  * @return \Symfony\Component\HttpFoundation\Response
    //  */
    // public function destroy(DeleteProfileRequest $request, UserProfileService $userProfileService): Response
    // {
    //     $userProfileService->delete($request->user(), $request->reason);

    //     return response('', Response::HTTP_NO_CONTENT);
    // }
}
