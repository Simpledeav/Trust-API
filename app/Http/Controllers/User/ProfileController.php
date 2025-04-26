<?php

namespace App\Http\Controllers\User;

use App\Models\Admin;
use App\Models\Trade;
use App\Models\Position;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SavingsLedger;
use App\Http\Controllers\Controller;
use App\Services\User\AnalyticsService;
use App\Services\User\UserProfileService;
use App\Services\User\ProfileTwoFaService;
use App\Services\User\ProfilePasswordService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\NotificationController;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Http\Requests\User\UpdateUserKycRequest;
use App\DataTransferObjects\Models\UserModelData;
use App\Http\Requests\User\UpdatePasswordRequest;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\Http\Requests\User\Profile\DeleteProfileRequest;
use App\Http\Requests\User\UpdateConnectWallet;

class ProfileController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get account data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Define allowed includes and the fields to be selected
        $allowedIncludes = [
            'currency' => ['id', 'name', 'symbol', 'sign'],
            'country' => ['id', 'name', 'phone_code'],
            'state' => ['id', 'name'],
            'city' => ['id', 'name'],
            'transactions' => ['id', 'amount', 'status', 'type', 'user_id'],
            'wallet' => ['id', 'balance', 'user_id'],
            'savings' => ['id', 'savings_account_id', 'user_id', 'balance'],
            'settings' => [
                'id', 
                'user_id', 
                'min_cash_deposit',
                'max_cash_deposit',
                'min_cash_withdrawal',
                'max_cash_withdrawal',
                'locked_cash',
                'locked_bank_deposit',
            ],
        ];

        // Get requested includes and filter only allowed ones
        $requestedIncludes = array_intersect(
            explode(',', $request->query('include', '')), 
            array_keys($allowedIncludes)
        );

        // Load requested relationships with selected fields
        if (!empty($requestedIncludes)) {
            foreach ($requestedIncludes as $relation) {
                $user->load([$relation => function ($query) use ($allowedIncludes, $relation) {
                    $query->select($allowedIncludes[$relation]);
                }]);
            }
        }

        // Append additional balances to the wallet object if wallet is loaded
        if ($user->relationLoaded('wallet') && $user->wallet) {

            // Helper function to calculate 24hr P&L and percentage change
            $calculate24hrPL = function ($accountType) use ($user) {
                // Fetch transactions for the last 24 hours
                $transactionsLast24h = Transaction::where('user_id', $user->id)
                    ->where('swap_from', $accountType)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->get();

                // Calculate net P&L
                $creditLast24h = $transactionsLast24h->where('type', 'credit')->where('status', 'approved')->sum('amount');
                $debitLast24h = $transactionsLast24h->where('type', 'debit')->where('status', 'approved')->sum('amount');
                $netPL = $creditLast24h - $debitLast24h;

                // Fetch balance 24 hours ago
                $balance24hAgo = Transaction::where('user_id', $user->id)
                    ->where('swap_from', $accountType)
                    ->where('status', 'approved')
                    ->where('created_at', '<', now()->subHours(24))
                    ->sum('amount');

                // Calculate percentage change
                $percentageChange = $balance24hAgo != 0
                    ? ($netPL / $balance24hAgo) * 100
                    : 0;

                return [
                    'balance' => number_format($user->wallet->getBalance($accountType), 2),
                    '24hr_pl' => number_format($netPL, 2),
                    '24hr_pl_percentage' => number_format($percentageChange, 2),
                ];
            };

            // Helper function to calculate total value for brokerage and auto accounts
            $calculateTotalValue = function ($accountType) use ($user) {
                // Fetch open positions for the account
                $positions = Position::where('user_id', $user->id)
                    ->where('account', $accountType)
                    ->where('status', 'open')
                    ->get();

                // Calculate total value of positions
                $totalPositionsValue = $positions->sum(function ($position) {
                    return $position->quantity * $position->asset->price + $position->extra;
                });

                // Total value = balance + positions value
                return $user->wallet->getBalance($accountType) + $totalPositionsValue;
            };
            
            // Helper function to calculate 24hr P&L and percentage change for brokerage and auto accounts
            // $calculate24hrPLForPositions = function ($accountType) use ($user) {
            //     // Fetch active positions for the account in the last 24 hours
            //     $positionsLast24h = Trade::where('user_id', $user->id)
            //         ->where('account', $accountType)
            //         ->where('type', 'buy')
            //         ->where('created_at', '>=', now()->subHours(24))
            //         ->get();

            //     $sellTradeLast24h = Trade::where('user_id', $user->id)
            //         ->where('account', $accountType)
            //         ->where('type', '')
            //         ->where('created_at', '>=', now()->subHours(24))
            //         ->get();

            //     $tradeLast24h = Position::where('user_id', $user->id)
            //         ->where('account', $accountType)
            //         ->where('status', 'open')
            //         ->where('created_at', '>=', now()->subHours(24))
            //         ->sum('extra');

            //     // Calculate total P&L for positions in the last 24 hours
            //     $totalPL = $positionsLast24h->sum(function ($position) {
            //         // Calculate profit/loss: (current price - opening price) * quantity + extra
            //         $currentPrice = $position->asset->price;
            //         $openingPrice = $position->price;
            //         $quantity = $position->quantity;
            //         $extra = $position->extra;

            //         // return ($currentPrice - $openingPrice) * $quantity + $extra;
            //         return ($currentPrice * $quantity + $extra) - $position->amount;
            //     }) + $tradeLast24h;

            //     // Fetch the total value of the account (balance + positions value)
            //     $totalValue = $user->wallet->getBalance($accountType) + $positionsLast24h->sum(function ($position) {
            //         return $position->quantity * $position->asset->price + $position->extra;
            //     });

            //     // Calculate percentage change
            //     $percentageChange = $totalValue != 0
            //         ? ($totalPL / $totalValue) * 100
            //         : 0;

            //     return [
            //         '24hr_pl' => number_format($totalPL, 2),
            //         '24hr_pl_percentage' => number_format($percentageChange, 2),
            //     ];
            // };

            // TEST:::: Helper function to calculate 24hr P&L and percentage change for brokerage and auto accounts
            $calculate24hrPLForPositions = function ($accountType) use ($user) {
                // Fetch active positions for the account in the last 24 hours
                $last24hrBuy = Trade::where('user_id', $user->id)
                    ->where('account', $accountType)
                    ->where('type', 'buy')
                    ->where('status', 'open')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->get();

                    $totalBuy = $last24hrBuy->sum(function ($position) {
                        // Calculate profit/loss: (current price - opening price) * quantity + extra
                        $currentPrice = $position->asset->price;
                        $quantity = $position->quantity;
                        $extra = $position->pl;
    
                        // return ($currentPrice - $openingPrice) * $quantity + $extra;
                        return ($currentPrice * $quantity) - $position->amount + $extra;
                    });

                $last24hrSell = Trade::where('user_id', $user->id)
                    ->where('account', $accountType)
                    ->where('type', 'sell')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->get();

                    $totalSell = $last24hrSell->sum(function ($position) {;
                        $extra = $position->pl;
    
                        return $extra;
                    });

                $totalPL = $totalBuy + $totalSell;

                // Fetch the total value of the account (balance + positions value)
                $totalValue = $user->wallet->getBalance($accountType);

                // Calculate percentage change
                $percentageChange = $totalValue != 0
                    ? ($totalPL / $totalValue) * 100
                    : 0;

                return [
                    '24hr_pl' => number_format($totalPL, 2),
                    '24hr_pl_percentage' => number_format($percentageChange, 2),
                ];
            };

            $savingsQuery = SavingsLedger::where('user_id', $user->id);
            $creditSavings = (clone $savingsQuery)->where('type', 'credit')->sum('amount');
            $debitSavings = (clone $savingsQuery)->where('type', 'debit')->sum('amount');
            $totalSavings = $creditSavings - $debitSavings;

            $total_investing = $calculateTotalValue('brokerage') + $calculateTotalValue('auto');
            $total_networth = $user->wallet->getBalance('wallet') + $total_investing + $totalSavings;

            // Build wallet response
            // $user->wallet->cash = $calculate24hrPL('wallet');
            $user->wallet->cash = [
                'balance' => number_format($user->wallet->getBalance('wallet'), 2),
                'total_networth' => number_format($total_networth, 2),
            ];
            $user->wallet->brokerage = [
                'balance' => number_format($user->wallet->getBalance('brokerage'), 2),
                'total' => number_format($calculateTotalValue('brokerage'), 2),
                '24hr_pl' => $calculate24hrPLForPositions('brokerage')['24hr_pl'],
                '24hr_pl_percentage' => $calculate24hrPLForPositions('brokerage')['24hr_pl_percentage'],
            ];
            $user->wallet->auto = [
                'balance' => number_format($user->wallet->getBalance('auto'), 2),
                'total' => number_format($calculateTotalValue('auto'), 2),
                '24hr_pl' => $calculate24hrPLForPositions('auto')['24hr_pl'],
                '24hr_pl_percentage' => $calculate24hrPLForPositions('auto')['24hr_pl_percentage'],
            ];
        }

        // Append savings account details and analysis data if savings is loaded
        if ($user->relationLoaded('savings') && $user->savings) {
            $user->savings->load(['savingsAccount' => function ($query) {
                $query->select(['id', 'name', 'title', 'status']); // Select fields for savings account
            }]);

            // Calculate savings analysis data for each savings account
            $user->savings->each(function ($savings) {
                $savingsQuery = SavingsLedger::where('savings_id', $savings->id);

                // Total Savings: Sum of contributions
                $creditSavings = (clone $savingsQuery)->where('type', 'credit')->where('method', 'contribution')->sum('amount');
                $debitSavings = (clone $savingsQuery)->where('type', 'debit')->where('method', 'contribution')->sum('amount');
                $savings->total_savings = number_format(($creditSavings), 2);

                // Total Return: (credit - contribution + profit)
                $creditTotalSavings = (clone $savingsQuery)->where('type', 'credit')->where('method', 'profit')->sum('amount');
                $debitTotalSavings = (clone $savingsQuery)->where('type', 'debit')->where('method', 'profit')->sum('amount');
                $savings->total_savings_return = number_format(($creditTotalSavings - $debitTotalSavings), 2);

                // 24hr Amount Change: Compare current savings with savings 24 hours ago
                $creditLast24h = (clone $savingsQuery)
                    ->where('type', 'credit')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->sum('amount');
                $debitLast24h = (clone $savingsQuery)
                    ->where('type', 'debit')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->sum('amount');
                $savingsLast24h = $creditLast24h - $debitLast24h; // Net change
                $savings->total_savings_24hr = number_format($savingsLast24h, 2);

                // Calculate 24hr Percentage Change
                $savingsBalance24hAgo = (clone $savingsQuery)
                    ->where('created_at', '<', now()->subHours(24))
                    ->where('created_at', '>=', now()->subHours(48)) // Ensure we're looking at the previous 24-hour window
                    ->sum('amount');

                $totalSavingsReturn = ($creditTotalSavings - $debitTotalSavings);
                // $totalSavings24hr = number_format($savingsLast24h, 2);

                if ($totalSavingsReturn  === 0)
                {
                    $savingsBalance24hPerctent = number_format(0, 2);
                } else {
                    $savingsBalance24hPerctent = ($totalSavingsReturn / $savingsLast24h) * 100;
                }
                 
                // Calculate percentage change
                if ($savingsBalance24hPerctent == 0) {
                    // If there was no savings 24 hours ago, percentage change is 0%
                    $savings->total_savings_24hr_percentage = 0;
                } else {
                    $savings->total_savings_24hr_percentage = number_format($savingsBalance24hPerctent, 2);
                }
            });
        }

        return ResponseBuilder::asSuccess()
            ->withMessage('Account fetched successfully')
            ->withData([
                'user' => $user,
            ])
            ->build();
    }

    public function analytics(Request $request)
    {
        try {
            $user = $request->user();
            $time = $request->timeframe ?? 'all';
            $timeframe = $request->query('timeframe', $time);

            $data = $this->analyticsService->getUserAnalytics($user, $timeframe);

            return ResponseBuilder::asSuccess()
                ->withMessage('Analytics fetched successfully')
                ->withData($data)
                ->build();
        } catch (\InvalidArgumentException $e) {
            return ResponseBuilder::asError(400)
                ->withMessage($e->getMessage())
                ->build();
        }
    }

    /**
     * Update profile password.
     *
     * @param \App\Http\Requests\User\Profile\UpdatePasswordRequest $request
     * @param \App\Services\Profile\ProfilePasswordService $profilePasswordService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePassword(
        UpdatePasswordRequest $request,
        ProfilePasswordService $profilePasswordService
    ): Response {
        $profilePasswordService->update($request->user(), $request->new_password);

        return ResponseBuilder::asSuccess()
            ->withMessage('Profile password updated successfully')
            ->build();
    }

    /**
     * Toggle Two-FA status.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\Profile\ProfileTwoFaService $profileTwoFaService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateTwoFa(Request $request, ProfileTwoFaService $profileTwoFaService): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $status = $profileTwoFaService->toggle($user);

        return ResponseBuilder::asSuccess()
            ->withMessage('Two-FA status updated successfully')
            ->withData([
                'status' => $status,
            ])
            ->build();
    }

    /**
     * Update profile.
     *
     * @param \App\Http\Requests\User\ProfileUpdateRequest $request
     * @param \App\Services\Profile\User\UserProfileService $userProfileService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateProfile(ProfileUpdateRequest $request, UserProfileService $userProfileService): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user = $userProfileService->update(
            $user,
            (new UserModelData())
                ->setFirstName($request->first_name)
                ->setLastName($request->last_name)
                ->setUsername($request->username)
                ->setPhoneNumber($request->phone)
                ->setDateOfBirth($request->dob)
                ->setAddress($request->address)
                ->setZipcode($request->zipcode)
                ->setSsn($request->ssn)
                ->setNationality($request->nationality)
                ->setCountryId($request->country_id)
                ->setStateId($request->state_id)
                ->setCity($request->city_id)
                ->setCurrencyId($request->currency_id)
                ->setAvatar($request->file('avatar')),
        );

        return ResponseBuilder::asSuccess()
            ->withMessage('Profile updated successfully')
            ->withData([
                'user' => $user,
            ])
            ->build();
    }

    public function updateKYC(UpdateUserKycRequest $request, UserProfileService $userProfileService): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user = $userProfileService->storeKycInfo(
            $user,
            (new UserModelData())
                ->setIdType($request->id_type)
                ->setIdNumber($request->id_number)
                ->setFrontId($request->file('front_id'))
                ->setBackId($request->file('back_id')),
        );

        return ResponseBuilder::asSuccess()
            ->withMessage('KYC updated successfully')
            ->withData([
                'user' => $user,
            ])
            ->build();
    }

    /**
     * Delete profile.
     *
     * @param \App\Http\Requests\User\Profile\DeleteProfileRequest $request
     * @param \App\Services\Profile\User\UserProfileService $userProfileService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, UserProfileService $userProfileService): Response
    {
        $userProfileService->destroy($request->user());

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Update wallet connection settings
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateWalletSettings(UpdateConnectWallet $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $user->settings->update($request->validated());

        return ResponseBuilder::asSuccess()
            ->withMessage('Wallet settings updated successfully')
            ->build();
    }

    /**
     * Toggle drip setting
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toggleDrip(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if($user->settings->drip == true)
            $drip = false;
        else
            $drip = true;

        $user->settings->update([
            'drip' => $drip
        ]);

        return ResponseBuilder::asSuccess()
            ->withMessage('Drip setting updated successfully')
            ->withData([
                'drip' => $user->settings->fresh()->drip
            ])
            ->build();
    }
}
