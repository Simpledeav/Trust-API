<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class SettingController extends Controller
{
    private ApiErrorCode $apiErrorCode = ApiErrorCode::ACCOUNT_DELETED_TEMPORARILY;
    
    public function index()
    {
        $settings = Setting::first();

        return view('admin.setting', [
            'settings' => $settings
        ]);
    }

    public function fetchSettings(Request $request): Response
    {
        // Get the first settings record
        $query = Setting::query();

        // Allow specifying the fields to return
        $allowedFields = $request->query('fields') 
            ? explode(',', $request->query('fields')) 
            : ['*']; // Default to all fields if none are specified

        $settings = $query->select($allowedFields)->first();

        if (!$settings) {
            return ResponseBuilder::asError($this->apiErrorCode->value)
                ->withHttpCode(Response::HTTP_NOT_ACCEPTABLE)
                ->withMessage('No settings found.')
                ->withMessage('Error updating account!')
                ->build();
        }

        return ResponseBuilder::asSuccess()
            ->withMessage('Settings fetched successfully')
            ->withData(['settings' => $settings])
            ->build();
    }


    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'btc_wallet' => ['nullable', 'string'],
            'eth_wallet' => ['nullable', 'string'],
            'trc_wallet' => ['nullable', 'string'],
            'erc_wallet' => ['nullable', 'string'],
            'wallet_note' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_number' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'bank_account_name' => ['nullable', 'string'],
            'bank_routing_number' => ['nullable', 'string'],
            'bank_reference' => ['nullable', 'string'],
            'bank_address' => ['nullable', 'string'],
        ]);

        if ($updated = Setting::all()->first()->update($validated))

        if ($updated) {
            return redirect()->back()->with('success', 'Settings updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update settings.');
    }
}
