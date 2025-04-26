<?php

namespace App\Services\User;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function create(array $data, $user)
    {
        $data['user_id'] = $user->id;
        return PaymentMethod::create($data);
    }

    public function update(PaymentMethod $paymentMethod, array $data)
    {
        $paymentMethod->update($data);
        return $paymentMethod;
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        return $paymentMethod->delete();
    }

    public function getUserMethods($user, $filters = [])
    {
        $query = $user->paymentMethods();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']); // 'bank' or 'crypto'
        }

        if (isset($filters['is_withdrawal'])) {
            $query->where('is_withdrawal', $filters['is_withdrawal']); // true or false
        }

        return $query->get();
    }
}
