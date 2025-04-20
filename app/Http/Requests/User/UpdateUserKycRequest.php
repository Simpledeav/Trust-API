<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserKycRequest extends FormRequest
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
            'id_type' => 'required|in:drivers_license,international_passport,national_passport',
            'id_number' => 'required|string|max:255',
            'front_id' => 'sometimes|file|mimes:jpeg,png,pdf|max:2048', // Adjust file validation rules as needed
            'back_id' => 'sometimes|file|mimes:jpeg,png,pdf|max:2048', // Adjust file validation rules as needed
        ];
    }
}
