<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class UpdateAssetsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update asset data in batches from FinancialModelingPrep API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Artisan::call('optimize:clear');

        $this->info('All caches cleared successfully!');
        
        // $batchSize = (int)$this->option('batch');
        $batchSize = 100;
        $apiKey = env('ASSET_KEY');
        
        Asset::chunkById($batchSize, function ($assets) use ($apiKey) {
            $symbols = $assets->pluck('symbol')->implode(',');
            $apiUrl = "https://financialmodelingprep.com/api/v3/quote/{$symbols}?apikey={$apiKey}";

            try {
                $response = Http::get($apiUrl);
                
                if ($response->successful()) {
                    $updatedData = $response->json();
                    
                    foreach ($updatedData as $assetData) {
                        Asset::where('symbol', $assetData['symbol'])
                            ->update([
                                'price' => $assetData['price'],
                                'changes_percentage' => $assetData['changesPercentage'],
                                'change' => $assetData['change'],
                                'day_low' => $assetData['dayLow'],
                                'day_high' => $assetData['dayHigh'],
                                'year_low' => $assetData['yearLow'],
                                'year_high' => $assetData['yearHigh'],
                                'market_cap' => $assetData['marketCap'],
                                'price_avg_50' => $assetData['priceAvg50'],
                                'price_avg_200' => $assetData['priceAvg200'],
                                'volume' => $assetData['volume'] ?? 0,
                                'avg_volume' => $assetData['avgVolume'] ?? 0,
                                'open' => $assetData['open'] ?? 0,
                                'previous_close' => $assetData['previousClose'] ?? 0,
                                'eps' => $assetData['eps'] ?? 0,
                                'pe' => $assetData['pe'] ?? 0,
                                'updated_at' => now(),
                            ]);
                    }
                    
                    $this->info("Updated batch: {$symbols}");
                } else {
                    Log::error("API request failed for symbols: {$symbols}", [
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error updating assets", [
                    'symbols' => $symbols,
                    'error' => $e->getMessage()
                ]);
            }
            
            sleep(1); // Rate limiting
        });
        
        $this->info('Asset data update completed.');
    }
}
