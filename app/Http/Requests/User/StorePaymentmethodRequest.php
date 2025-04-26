<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentmethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:bank,crypto',
            'label' => 'nullable|string',
            'currency' => 'nullable|string',
            'wallet_address' => 'nullable|string',
            'account_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'routing_number' => 'nullable|string',
            'bank_reference' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'is_withdrawal' => 'nullable|boolean',
        ];
    }
}
