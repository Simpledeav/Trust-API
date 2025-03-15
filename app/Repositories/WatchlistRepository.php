<?php

namespace App\Repositories;

use App\Models\Watchlist;
use Illuminate\Support\Collection;

class WatchlistRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function fetch($userId): Collection
    {
        return Watchlist::with('asset')
            ->where('user_id', $userId)
            ->get();
    }

    public function store($userId, $assetId, $comment = null): Watchlist
    {
        return Watchlist::create([
            'user_id'  => $userId,
            'asset_id' => $assetId,
            'comment'  => $comment,
        ]);
    }

    public function destroy($userId, $watchlistId): bool
    {
        return Watchlist::where('user_id', $userId)
            ->where('id', $watchlistId)
            ->delete();
    }
}
