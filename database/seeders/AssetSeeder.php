<?php

namespace Database\Seeders;

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
            'AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META', 'TSLA', 'BRK.B', 'V', 'JNJ',
            'WMT', 'PG', 'JPM', 'UNH', 'MA', 'XOM', 'LLY', 'HD', 'CVX', 'MRK',
            'ABBV', 'PEP', 'KO', 'COST', 'AVGO', 'PFE', 'TMO', 'NKE', 'MCD', 'DIS',
            'CSCO', 'ACN', 'VZ', 'DHR', 'ADBE', 'CMCSA', 'NFLX', 'TXN', 'NEE', 'CRM',
            'ABT', 'BMY', 'WFC', 'INTC', 'LIN', 'ORCL', 'UPS', 'PM', 'RTX', 'AMD',
            
            'BTCUSD', 'ETHUSD', 'XRPUSD', 'LTCUSD', 'BCHUSD', 'ADAUSD', 'DOGEUSD', 'DOTUSD', 'SOLUSD', 'BNBUSD',
            'AVAXUSD', 'MATICUSD', 'UNIUSD', 'LINKUSD', 'XLMUSD', 'TRXUSD', 'ETCUSD', 'FILUSD', 'ALGOUSD', 'VETUSD',
            'THETAUSD', 'ATOMUSD', 'ICPUSD', 'XMRUSD', 'EOSUSD', 'AAVEUSD', 'MKRUSD', 'SUSHIUSD', 'COMPUSD', 'YFIUSD',
            'ZECUSD', 'DASHUSD', 'NEOUSD', 'QTUMUSD', 'WAVESUSD', 'ZILUSD', 'ONTUSD', 'NANOUSD', 'ICXUSD', 'BATUSD',
            'RENUSD', 'OMGUSD', 'ENJUSD', 'ANKRUSD', 'CHZUSD', 'KSMUSD', 'STXUSD', 'GRTUSD', 'SNXUSD', 'RUNEUSD'
        ];
        
        $apiKey = 'U16Gq0PRKGgnTbltSa5423seAWtQNV0T';
        $batchSize = 100;
        
        // Separate symbols into stocks and crypto
        $stockSymbols = [];
        $cryptoSymbols = [];
        
        foreach ($symbols as $symbol) {
            if (str_ends_with($symbol, 'USD')) {
                $cryptoSymbols[] = $symbol;
            } else {
                $stockSymbols[] = $symbol;
            }
        }
        
        // Process stocks
        if (!empty($stockSymbols)) {
            $stockChunks = array_chunk($stockSymbols, $batchSize);
            foreach ($stockChunks as $chunk) {
                $symbolsString = implode(',', $chunk);
                $apiUrl = "https://financialmodelingprep.com/api/v3/quote/{$symbolsString}?apikey={$apiKey}";
        
                try {
                    $response = Http::get($apiUrl);
        
                    if ($response->failed()) {
                        $this->command->error("Failed to fetch stock data for symbols: {$symbolsString}");
                        continue;
                    }
        
                    $stocks = $response->json();
                    if (empty($stocks)) {
                        $this->command->warn("No stock data returned for symbols: {$symbolsString}");
                        continue;
                    }
        
                    $stocksData = [];
                    foreach ($stocks as $stockData) {
                        $stocksData[] = [
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'symbol' => $stockData['symbol'],
                            'name' => $stockData['name'],
                            'img' => "https://images.financialmodelingprep.com/symbol/{$stockData['symbol']}.png",
                            'price' => $stockData['price'],
                            'changes_percentage' => $stockData['changesPercentage'],
                            'change' => $stockData['change'],
                            'day_low' => $stockData['dayLow'],
                            'day_high' => $stockData['dayHigh'],
                            'year_low' => $stockData['yearLow'],
                            'year_high' => $stockData['yearHigh'],
                            'market_cap' => $stockData['marketCap'],
                            'price_avg_50' => $stockData['priceAvg50'],
                            'price_avg_200' => $stockData['priceAvg200'],
                            'exchange' => $stockData['exchange'],
                            'volume' => $stockData['volume'] ?? 0,
                            'avg_volume' => $stockData['avgVolume'] ?? 0,
                            'open' => $stockData['open'] ?? 0,
                            'previous_close' => $stockData['previousClose'] ?? 0,
                            'eps' => $stockData['eps'] ?? 0,
                            'pe' => $stockData['pe'] ?? 0,
                            'type' => 'stocks', // Explicitly set type for stocks
                            'status' => 'active',
                            'tradeable' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
        
                    DB::table('assets')->upsert($stocksData, ['symbol'], array_keys($stocksData[0]));
                    $this->command->info("Successfully inserted/updated stocks for batch: {$symbolsString}");
                } catch (\Exception $e) {
                    $this->command->error("Error processing stock symbols: {$symbolsString} - " . $e->getMessage());
                    Log::error("Error in StockSeeder (Stocks)", ['symbols' => $symbolsString, 'error' => $e->getMessage()]);
                }
            }
        }
        
        // Process crypto
        if (!empty($cryptoSymbols)) {
            $cryptoChunks = array_chunk($cryptoSymbols, $batchSize);
            foreach ($cryptoChunks as $chunk) {
                $symbolsString = implode(',', $chunk);
                $apiUrl = "https://financialmodelingprep.com/api/v3/quote/{$symbolsString}?apikey={$apiKey}";
        
                try {
                    $response = Http::get($apiUrl);
        
                    if ($response->failed()) {
                        $this->command->error("Failed to fetch crypto data for symbols: {$symbolsString}");
                        continue;
                    }
        
                    $cryptos = $response->json();
                    if (empty($cryptos)) {
                        $this->command->warn("No crypto data returned for symbols: {$symbolsString}");
                        continue;
                    }
        
                    $cryptoData = [];
                    foreach ($cryptos as $crypto) {
                        $cryptoData[] = [
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'symbol' => $crypto['symbol'],
                            'name' => $crypto['name'],
                            'img' => "https://images.financialmodelingprep.com/symbol/{$crypto['symbol']}.png",
                            'price' => $crypto['price'],
                            'changes_percentage' => $crypto['changesPercentage'],
                            'change' => $crypto['change'],
                            'day_low' => $crypto['dayLow'],
                            'day_high' => $crypto['dayHigh'],
                            'year_low' => $crypto['yearLow'],
                            'year_high' => $crypto['yearHigh'],
                            'market_cap' => $crypto['marketCap'],
                            'price_avg_50' => $crypto['priceAvg50'],
                            'price_avg_200' => $crypto['priceAvg200'],
                            'exchange' => $crypto['exchange'],
                            'volume' => $crypto['volume'] ?? 0,
                            'avg_volume' => $crypto['avgVolume'] ?? 0,
                            'open' => $crypto['open'] ?? 0,
                            'previous_close' => $crypto['previousClose'] ?? 0,
                            'eps' => $crypto['eps'] ?? 0,
                            'pe' => $crypto['pe'] ?? 0,
                            'type' => 'crypto', // Explicitly set type for crypto
                            'status' => 'active',
                            'tradeable' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
        
                    DB::table('assets')->upsert($cryptoData, ['symbol'], array_keys($cryptoData[0]));
                    $this->command->info("Successfully inserted/updated crypto for batch: {$symbolsString}");
                } catch (\Exception $e) {
                    $this->command->error("Error processing crypto symbols: {$symbolsString} - " . $e->getMessage());
                    Log::error("Error in StockSeeder (Crypto)", ['symbols' => $symbolsString, 'error' => $e->getMessage()]);
                }
            }
        }
        
        $this->command->info("Stock and crypto seeding completed.");
    }
}
