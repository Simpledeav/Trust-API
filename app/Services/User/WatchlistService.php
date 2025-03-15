<?php

namespace App\Services\User;

use App\Repositories\WatchlistRepository;

class WatchlistService
{
    protected WatchlistRepository $watchlistRepository;

    public function __construct(WatchlistRepository $watchlistRepository)
    {
        $this->watchlistRepository = $watchlistRepository;
    }

    public function fetch($userId)
    {
        return $this->watchlistRepository->fetch($userId);
    }

    public function store($userId, $assetId, $comment = null)
    {
        return $this->watchlistRepository->store($userId, $assetId, $comment);
    }

    public function destroy($userId, $watchlistId)
    {
        return $this->watchlistRepository->destroy($userId, $watchlistId);
    }
}
