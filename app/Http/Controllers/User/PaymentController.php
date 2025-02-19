<?php

namespace App\Http\Controllers\User;

use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\PaymentService;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class PaymentController extends Controller
{
    protected $paymentService;

    private ApiErrorCode $apiErrorCode = ApiErrorCode::ACCOUNT_DELETED_TEMPORARILY;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function updatePayment(Request $request, string $type = 'user'): Response
    {
        $request->validate([
            'wallet_name' => 'nullable|string',
            'wallet_address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_number' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_routing_number' => 'nullable|string',
            'bank_reference' => 'nullable|string',
            'bank_address' => 'nullable|string',
        ]);

        try {
            $updatedPayment = $this->paymentService->updatePayment($request->user(), $type, $request->all());

            return ResponseBuilder::asSuccess()
            ->withMessage('Account updated successfully')
            ->withData([
                'account' => $updatedPayment,
            ])
            ->build();
        } catch (\Exception $e) {
            return ResponseBuilder::asError($this->apiErrorCode->value)
                ->withHttpCode(Response::HTTP_NOT_ACCEPTABLE)
                // ->withMessage($this->getMessage() ?: $this->apiErrorCode->description())
                ->withMessage('Error updating account!')
                ->build();
        }
    }
}
