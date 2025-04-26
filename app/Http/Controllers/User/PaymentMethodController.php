<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Services\User\PaymentMethodService;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\Http\Requests\User\StorePaymentmethodRequest;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $service) {}
    
    public function index(Request $request): Response
    {
        $paymentMethods = QueryBuilder::for(
            PaymentMethod::query()->where('user_id', $request->user()->id)
        )
            ->select([
                'id',
                'user_id',
                'type',
                'label',
                'currency',
                'wallet_address',
                'account_name',
                'account_number',
                'bank_name',
                'routing_number',
                'bank_reference',
                'bank_address',
                'is_withdrawal',
                'created_at',
            ])
            ->allowedFilters([
                AllowedFilter::exact('type'), // 'bank' or 'crypto'
                AllowedFilter::exact('is_withdrawal'), // true or false
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'currency', 'type'])
            ->paginate((int) $request->per_page ?: 15)
            ->through(function ($method) {
                // Optional: filter out irrelevant fields for better frontend response
                return collect($method)->only(
                    $method->type === 'bank'
                        ? ['id', 'label', 'type', 'currency', 'account_name', 'account_number', 'bank_name', 'routing_number', 'bank_reference', 'bank_address', 'is_withdrawal', 'created_at']
                        : ['id', 'label', 'type', 'currency', 'wallet_address', 'is_withdrawal', 'created_at']
                );
            })
            ->withQueryString();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Payment methods fetched successfully',
            'data' => ['payment_methods' => $paymentMethods],
        ], Response::HTTP_OK);
    }
    

    public function store(StorePaymentmethodRequest $request): Response
    {
        try {
            $payment = $this->service->create($request->validated(), $request->user());

            return ResponseBuilder::asSuccess()
            ->withMessage('Payment method created successfully')
            ->withData([
                'payment' => $payment,
            ])
            ->build();
        } catch (\Exception $e) {
            return ResponseBuilder::asError(500)
                ->withHttpCode(Response::HTTP_NOT_ACCEPTABLE)
                ->withMessage('Error storing payment method!')
                ->build();
        }
    }

    public function update(PaymentMethod $payment, StorePaymentmethodRequest $request): Response
    {
        try {
            $payment = $this->service->update($payment, $request->validated());

            return ResponseBuilder::asSuccess()
            ->withMessage('Payment method updated successfully')
            ->withData([
                'payment' => $payment,
            ])
            ->build();
        } catch (\Exception $e) {
            return ResponseBuilder::asError(500)
                ->withHttpCode(Response::HTTP_NOT_ACCEPTABLE)
                ->withMessage('Error updating payment method!')
                ->build();
        }
    }

    public function destroy(PaymentMethod $payment): Response
    {
        $this->service->delete($payment);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
