<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use App\Models\Setting;
use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class SettingController extends Controller
{
    private ApiErrorCode $apiErrorCode = ApiErrorCode::ACCOUNT_DELETED_TEMPORARILY;
    
    public function index()
    {
        $settings = Setting::first();

        $newest_updated = Asset::orderBy('updated_at', 'desc')->value('updated_at');

        // dd($newest_updated);

        return view('admin.setting', [
            'settings' => $settings,
            'asset_time' => $newest_updated
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
            'min_cash_deposit' => ['nullable', 'string'],
            'max_cash_deposit' => ['nullable', 'string'],
            'min_cash_withdrawal' => ['nullable', 'string'],
            'max_cash_withdrawal' => ['nullable', 'string'],
        ]);

        if ($updated = Setting::all()->first()->update($validated))

        if ($updated) {
            return redirect()->back()->with('success', 'Settings updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update settings.');
    }

    public function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        
        // Clear any custom caches you might have
        Cache::flush();
        
        return back()->with('success', 'All caches have been cleared successfully.');
    }

    public function refreshPrice(Request $request)
    {
        Artisan::call('assets:update');
        
        Cache::flush();
        
        return back()->with('success', 'Asset updated successfully.');
    }
}
