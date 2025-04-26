<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\User\PaymentMethodService;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $service) {}
    
    public function store(Request $request, User $user)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
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
            // 'is_withdrawal' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }
        
        $data = $validator->validated();
        $data['user_id'] = $user->id;
        $data['is_withdrawal'] = $request->has('is_withdrawal');

        PaymentMethod::create($data);

        return redirect()->back()->with('success', 'Payment method stored successfully');
    }

    public function update(Request $request, PaymentMethod $payment)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
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
            // 'is_withdrawal' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Invalid input data');
        }

        $data = $validator->validated();

        $data['is_withdrawal'] = $request->has('is_withdrawal');
        $payment->update($data);

        return redirect()->back()->with('success', 'Payment method updated successfully');
    }

    public function destroy(PaymentMethod $payment)
    {
        $payment->delete();

        return redirect()->back()->with('success', 'Payment method deleted successfully');
    }
}
