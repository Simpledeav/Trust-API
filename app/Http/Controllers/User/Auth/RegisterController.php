<?php

namespace App\Http\Controllers\User\Auth;

use App\DataTransferObjects\Models\UserModelData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Services\User\UserService;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\User\Auth\RegisterRequest $request
     * @param \App\Services\User\UserService $userService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(RegisterRequest $request, UserService $userService): Response
    {
        $user = $userService->create(
            userModelData: (new UserModelData())
                ->setFirstName($request->first_name)
                ->setLastName($request->last_name)
                ->setEmail($request->email)
                ->setPassword($request->password)
                ->setUsername($request->username)
                ->setPhoneNumber($request->phone)
                ->setDateOfBirth($request->date_of_birth)
                ->setRefCode($request->ref)
                ->setAddress($request->address)  // Assuming these fields are part of the request
                ->setZipcode($request->zipcode)
                ->setSsn($request->ssn)
                ->setNationality($request->nationality)
                ->setExperience($request->experience)
                ->setEmployed($request->employed)
                ->setIdNumber($request->id_number)
                ->setFrontId($request->front_id)
                ->setBackId($request->back_id)
                ->setCountryId($request->country_id)
                ->setStateId($request->state_id)
                ->setCityId($request->city_id)
                ->setCurrencyId($request->currency_id),
            authenticate: true
        );
        

        return ResponseBuilder::asSuccess()
            ->withHttpCode(201)
            ->withMessage($user->getApiMessage())
            ->withData([
                'user' => $user->getUser(),
                'token' => $user->getToken(),
            ])
            ->build();
    }
}
