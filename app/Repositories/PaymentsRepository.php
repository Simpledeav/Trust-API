<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Payment;

class PaymentsRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function updatePayment(User $user, string $type, array $data): Payment
    {
        $payment = $user->payments()->where('type', $type)->firstOrFail();
        $payment->update($data);
        return $payment;
    }
}
