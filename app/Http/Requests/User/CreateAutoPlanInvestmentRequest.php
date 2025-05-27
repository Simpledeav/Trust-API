<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateAutoPlanInvestmentRequest extends FormRequest
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
            'auto_plan_id' => ['required', 'exists:auto_plans,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            // 'wallet' => ['required', Rule::in(['brokerage', 'auto'])],
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'wallet.in' => 'Wallet must be either brokerage or auto',
    //     ];
    // }
}
