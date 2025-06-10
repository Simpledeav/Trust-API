<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettings extends FormRequest
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
            // Beneficiary fields
            'beneficiary_first_name' => 'sometimes|string|max:255',
            'beneficiary_last_name' => 'sometimes|string|max:255',
            'beneficiary_nationality' => 'sometimes|string|max:255',
            'beneficiary_dob' => 'sometimes|date',
            'beneficiary_email' => 'sometimes|email|max:255',
            'beneficiary_phone' => 'sometimes|string|max:20',
            'beneficiary_address' => 'sometimes|string|max:500',
            'beneficiary_country' => 'sometimes|string|max:255',
            'beneficiary_state' => 'sometimes|string|max:255',
            'beneficiary_city' => 'sometimes|string|max:255',
            'beneficiary_zipcode' => 'sometimes|string|max:20',
        ];
    }
}
