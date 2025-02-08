<?php

namespace App\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class LoginController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @param \App\Services\Auth\LoginService $loginService
     */
    public function __construct(private LoginService $loginService)
    {
    }

     /**
     * Start authenticated session.
     *
     * @param \App\Http\Requests\User\Auth\LoginRequest $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(LoginRequest $request): Response
    {
        $usernameColumn = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $authenticationCredentials = $this->loginService->start(
            $usernameColumn,
            $request->login,
            $request->password,
            (new User())
        );

        return ResponseBuilder::asSuccess()
            ->withMessage($authenticationCredentials->getApiMessage())
            ->withData([
                'user' => $authenticationCredentials->getUser(),
                'token' => $authenticationCredentials->getToken(),
                'requires_two_fa' => $authenticationCredentials->getTwoFaRequired(),
            ])
            ->build();
    }

    /**
     * End authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request): Response
    {
        $this->loginService->stop($request->user());

        return ResponseBuilder::asSuccess()
            ->withMessage('Logout was successful.')
            ->build();
    }

    /**
     * End authenticated sessions on other devices, except the current one.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logoutOtherDevices(Request $request): Response
    {
        $this->loginService->stopOthers($request->user());

        return ResponseBuilder::asSuccess()
            ->withMessage('All other devices have been logged-out successfully.')
            ->build();
    }
}
