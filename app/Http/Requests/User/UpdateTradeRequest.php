<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTradeRequest extends FormRequest
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
            'price'    => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|numeric|min:0',
            'amount'   => 'sometimes|required|numeric|min:0',
            'status'   => 'sometimes|required|in:open,close,hold',
            'entry'    => 'nullable|string',
            'exit'     => 'nullable|string',
            'leverage' => 'nullable|string',
            'interval' => 'nullable|string',
            'tp'       => 'nullable|string',
            'sl'       => 'nullable|string',
            'extra'    => 'nullable|numeric|min:0',
        ];
    }
}
