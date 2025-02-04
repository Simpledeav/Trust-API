<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavingsRequest extends FormRequest
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
            'savings_account_id' => [
                'required',
                'exists:savings_accounts,id',
                function ($attribute, $value, $fail) {
                    $user = $this->user(); // Get the authenticated user
                    $savingsAccount = \App\Models\SavingsAccount::find($value); // Get the savings account by ID

                    // Ensure the savings account exists
                    if ($savingsAccount) {
                        // Decode the country IDs associated with the savings account
                        $savingsAccountCountries = json_decode($savingsAccount->country_id, true);

                        // Check if the user's country is in the list of available countries for the savings account
                        if (!in_array($user->country_id, $savingsAccountCountries)) {
                            $fail('Savings Account is not available for your country');
                        }

                        // Prevent duplicate savings account type for the user
                        if ($user->savings()->where('savings_account_id', $value)->exists()) {
                            $fail('You already have this savings account.');
                        }
                    }
                }
            ],
        ];
    }
}
