<?php

namespace App\Services\User;

use App\Models\User;
use App\Models\Payment;
use App\Repositories\PaymentsRepository;

class PaymentService
{
    protected $paymentRepository;

    public function __construct(PaymentsRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function updatePayment(User $user, string $type, array $data): Payment
    {
        return $this->paymentRepository->updatePayment($user, $type, $data);
    }
}
