<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreTradeRequest extends FormRequest
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
            'asset_id'   => 'required|uuid|exists:assets,id',
            'asset_type' => 'required|in:crypto,stock',
            'type'       => 'required|in:buy,sell',
            // 'price'      => 'required|numeric|min:0',
            'quantity'   => 'required|numeric|min:0',
            // 'amount'     => 'required|numeric|min:0',
            'status'     => 'nullable|in:open,close,hold',
            'entry'      => 'nullable|string',
            'exit'       => 'nullable|string',
            'leverage'   => 'nullable|string',
            'interval'   => 'nullable|string',
            'tp'         => 'nullable|string',
            'sl'         => 'nullable|string',
        ];
    }
}
