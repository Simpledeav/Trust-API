<?php

namespace App\Http\Requests\User\Auth;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    private string|null $countryCode;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function authorize(): bool|\Illuminate\Auth\Access\Response
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // $this->countryCode = Country::query()
        //     ->where('id', $this->country_id)
        //     ->whereNotNull('registration_activated_at')
        //     ->value('alpha2_code');

        return [
            'country_id' => 'required|uuid|exists:countries,id',
            'state_id' => 'required|uuid|exists:states,id',
            'city_id' => 'required|uuid|exists:cities,id',
            'currency_id' => 'required|uuid|exists:currencies,id',
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => [
                'required',
                Rule::when(app()->environment('production'), 'email:rfc,dns', 'email'),
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'confirmed',
            ],
            'username' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'username'),
            ],
            'phone' => 'required|string|max:15',  // Adjusted for phone number validation
            'address' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'ssn' => 'required|string|max:20',
            'dob' => 'nullable|date',
            'nationality' => 'required|string|max:191',
            'experience' => 'nullable|string|max:191',
            'employed' => 'nullable|string|max:191',
            'id_number' => 'nullable|string|max:191',
            'front_id' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240', // Assuming file upload for IDs
            'back_id' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240', // Assuming file upload for IDs
            
        ];
    }
}
