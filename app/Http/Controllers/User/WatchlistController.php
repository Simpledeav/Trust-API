<?php

namespace App\Http\Controllers\User;

use App\Enums\ApiErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\WatchlistService;
use Symfony\Component\HttpFoundation\Response;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class WatchlistController extends Controller
{
    protected WatchlistService $watchlistService;

    public function __construct(WatchlistService $watchlistService)
    {
        $this->watchlistService = $watchlistService;
    }

    /**
     * Fetch the user's watchlist.
     */
    public function index(Request $request): Response
    {
        $watchlist = $this->watchlistService->fetch($request->user()->id);

        return ResponseBuilder::asSuccess()
            ->withMessage('Watchlist fetched successfully')
            ->withData(['watchlist' => $watchlist])
            ->build();
    }

    /**
     * Add asset to the watchlist.
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'asset_id'   => 'required|uuid|exists:assets,id',
            'comment'  => 'nullable|string',
        ]);

        $watchlist = $this->watchlistService->store(
            $request->user()->id,
            $request->asset_id,
            $request->comment
        );

        return ResponseBuilder::asSuccess()
            ->withMessage('Asset added to watchlist')
            ->withData(['watchlist' => $watchlist])
            ->build();
    }

    /**
     * Remove asset from the watchlist.
     */
    public function destroy(Request $request, $watchlistId): Response
    {
        $watchlist = $this->watchlistService->destroy($request->user()->id, $watchlistId);

        if ($watchlist) {
            return ResponseBuilder::asSuccess()
                ->withMessage('Watchlist deleted successfully')
                ->build();

        } else {
            return ResponseBuilder::asError(ApiErrorCode::GENERAL_ERROR->value)
                ->withMessage('Error trying to delete watchlist.')
                ->build();
        }
    }
}
