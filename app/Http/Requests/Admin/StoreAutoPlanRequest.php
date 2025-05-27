<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoPlanRequest extends FormRequest
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
            'name' => 'required|string|unique:auto_plans,name',
            'min_invest' => 'required|numeric|min:0',
            'max_invest' => 'required|numeric|min:0',
            'win_rate' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'milestone' => 'required|string',
            'aum' => 'required|string',
            'returns' => 'required|string',
            'img' => 'required',
        ];
    }
}
