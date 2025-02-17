<?php

namespace App\Providers;

use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use App\Repositories\SavingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Repositories\SavingsAccountRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Contracts\Repositories\SavingsAccountRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SavingsAccountRepositoryInterface::class, SavingsAccountRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Model::shouldBeStrict();
    }
}
