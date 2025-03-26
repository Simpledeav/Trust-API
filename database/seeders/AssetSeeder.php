<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $symbols = [
            // Stocks
            'AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META', 'TSLA', 'BRK.B', 'V', 'JNJ',
            'WMT', 'PG', 'JPM', 'UNH', 'MA', 'XOM', 'LLY', 'HD', 'CVX', 'MRK',
            'ABBV', 'PEP', 'KO', 'COST', 'AVGO', 'PFE', 'TMO', 'NKE', 'MCD', 'DIS',
            'CSCO', 'ACN', 'VZ', 'DHR', 'ADBE', 'CMCSA', 'NFLX', 'TXN', 'NEE', 'CRM',
            'ABT', 'BMY', 'WFC', 'INTC', 'LIN', 'ORCL', 'UPS', 'PM', 'RTX', 'AMD',
            
            // Crypto (USD pairs)
            'BTCUSD', 'ETHUSD', 'XRPUSD', 'LTCUSD', 'BCHUSD', 'ADAUSD', 'DOGEUSD', 'DOTUSD', 'SOLUSD', 'BNBUSD',
            'AVAXUSD', 'MATICUSD', 'UNIUSD', 'LINKUSD', 'XLMUSD', 'TRXUSD', 'ETCUSD', 'FILUSD', 'ALGOUSD', 'VETUSD',
            'THETAUSD', 'ATOMUSD', 'ICPUSD', 'XMRUSD', 'EOSUSD', 'AAVEUSD', 'MKRUSD', 'SUSHIUSD', 'COMPUSD', 'YFIUSD',
            'ZECUSD', 'DASHUSD', 'NEOUSD', 'QTUMUSD', 'WAVESUSD', 'ZILUSD', 'ONTUSD', 'NANOUSD', 'ICXUSD', 'BATUSD',
            'RENUSD', 'OMGUSD', 'ENJUSD', 'ANKRUSD', 'CHZUSD', 'KSMUSD', 'STXUSD', 'GRTUSD', 'SNXUSD', 'RUNEUSD'
        ];
        
        $apiKey = 'U16Gq0PRKGgnTbltSa5423seAWtQNV0T';
        $batchSize = 100;
        
        // Process symbols in batches with their types
        $symbolTypes = [
            'stocks' => array_filter($symbols, fn($s) => !str_ends_with($s, 'USD')),
            'crypto' => array_filter($symbols, fn($s) => str_ends_with($s, 'USD'))
        ];
        
        foreach ($symbolTypes as $type => $symbolsOfType) {
            if (empty($symbolsOfType)) continue;
            
            $chunks = array_chunk($symbolsOfType, $batchSize);
            
            foreach ($chunks as $chunk) {
                $symbolsString = implode(',', $chunk);
                $apiUrl = "https://financialmodelingprep.com/api/v3/quote/{$symbolsString}?apikey={$apiKey}";
        
                try {
                    $response = Http::get($apiUrl);
                    
                    if ($response->failed()) {
                        $this->command->error("Failed to fetch {$type} data for: {$symbolsString}");
                        continue;
                    }
        
                    $assets = $response->json();
                    if (empty($assets)) {
                        $this->command->warn("No {$type} data returned for: {$symbolsString}");
                        continue;
                    }
        
                    $assetsData = array_map(function ($asset) use ($type) {
                        return [
                            'id' => (string) Str::uuid(),
                            'symbol' => $asset['symbol'],
                            'name' => $asset['name'],
                            'img' => "https://images.financialmodelingprep.com/symbol/{$asset['symbol']}.png",
                            'price' => $asset['price'],
                            'changes_percentage' => $asset['changesPercentage'],
                            'change' => $asset['change'],
                            'day_low' => $asset['dayLow'],
                            'day_high' => $asset['dayHigh'],
                            'year_low' => $asset['yearLow'],
                            'year_high' => $asset['yearHigh'],
                            'market_cap' => $asset['marketCap'],
                            'price_avg_50' => $asset['priceAvg50'],
                            'price_avg_200' => $asset['priceAvg200'],
                            'exchange' => $asset['exchange'],
                            'volume' => $asset['volume'] ?? 0,
                            'avg_volume' => $asset['avgVolume'] ?? 0,
                            'open' => $asset['open'] ?? 0,
                            'previous_close' => $asset['previousClose'] ?? 0,
                            'eps' => $asset['eps'] ?? 0,
                            'pe' => $asset['pe'] ?? 0,
                            'type' => $type,  // Dynamic type assignment
                            'status' => 'active',
                            'tradeable' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }, $assets);
        
                    DB::table('assets')->upsert($assetsData, ['symbol'], array_keys($assetsData[0]));
                    $this->command->info("Successfully processed {$type} batch: {$symbolsString}");
                    
                } catch (\Exception $e) {
                    $this->command->error("Error processing {$type} symbols: {$symbolsString} - {$e->getMessage()}");
                    Log::error("Error in {$type} seeder", ['symbols' => $symbolsString, 'error' => $e->getMessage()]);
                }
            }
        }
        
        $this->command->info("Asset seeding completed for all types.");

    }
}
