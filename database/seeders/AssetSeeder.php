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
        // $symbols = [
        //     'AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META', 'TSLA', 'BRK.B', 'V', 'JNJ',
        //     'WMT', 'PG', 'JPM', 'UNH', 'MA', 'XOM', 'LLY', 'HD', 'CVX', 'MRK',
        //     'ABBV', 'PEP', 'KO', 'COST', 'AVGO', 'PFE', 'TMO', 'NKE', 'MCD', 'DIS',
        //     'CSCO', 'ACN', 'VZ', 'DHR', 'ADBE', 'CMCSA', 'NFLX', 'TXN', 'NEE', 'CRM',
        //     'ABT', 'BMY', 'WFC', 'INTC', 'LIN', 'ORCL', 'UPS', 'PM', 'RTX', 'AMD',
        //     'SBUX', 'HON', 'CVS', 'LOW', 'IBM', 'QCOM', 'UNP', 'MS', 'INTU', 'SPGI',
        //     'MDT', 'SCHW', 'GS', 'BLK', 'T', 'PLD', 'BKNG', 'ADP', 'CAT', 'ELV',
        //     'ISRG', 'NOW', 'MU', 'DE', 'LMT', 'AXP', 'GILD', 'SYK', 'ZTS', 'USB',
        //     'TGT', 'AMAT', 'GE', 'MMC', 'ADI', 'MDLZ', 'MO', 'ADSK', 'PNC', 'CI',
        //     'EW', 'ITW', 'MRNA', 'CB', 'C', 'CL', 'BA', 'CSX', 'HUM', 'DUK',
        //     'MNST', 'BDX', 'BSX', 'NSC', 'AON', 'FIS', 'VRTX', 'PGR', 'GM', 'FDX',
        //     'APD', 'EMR', 'SHW', 'KMB', 'ECL', 'CHTR', 'REGN', 'ETN', 'AEP', 'COF',
        //     'DAL', 'AIG', 'WBA', 'EA', 'SLB', 'STZ', 'BIIB', 'TT', 'PRU', 'CTSH',
        //     'MPC', 'LRCX', 'HCA', 'TRV', 'PSA', 'AFL', 'VLO', 'SRE', 'LUV', 'D',
        //     'COO', 'KMI', 'HLT', 'KHC', 'XEL', 'PEG', 'ED', 'DTE', 'SO', 'AEE',
        //     'WEC', 'AWK', 'EIX', 'CMS', 'OKE', 'PCG', 'NI', 'AES', 'ATO', 'ESS',
        //     'ARE', 'AMT', 'SBAC', 'CCI', 'EXR', 'BXP', 'BAM', 'NTRS', 'ICE', 'VTR',
        //     'FISV', 'MCO', 'DHI', 'PSX', 'MCHP', 'RF', 'TSCO', 'K', 'CNP', 'AMP',
        //     'SYY', 'CERN', 'GLW', 'NUE', 'MOS', 'LYB', 'ROK', 'KEY', 'HIG', 'HPE',
        //     'ADM', 'CAG', 'WDC', 'IP', 'F', 'WY', 'LEN', 'PPL', 'HES', 'IPG',
        //     'MTD', 'RHI', 'FTV', 'CINF', 'OMC', 'MKC', 'LKQ', 'HPQ', 'ZBH', 'AVB',
        //     'RCL', 'CPB', 'SJM', 'IRM', 'CLX', 'VFC', 'BBY', 'DGX', 'APA', 'CMA',
        //     'BKR', 'TXT', 'PH', 'URI', 'RJF', 'HRL', 'SEE', 'ETR', 'RSG', 'RMD',
        //     'CTAS', 'PAYX', 'LDOS', 'TROW', 'DG', 'BBWI', 'LVS', 'BWA', 'AAP', 'MKTX',
        //     'NVR', 'AZO', 'MTCH', 'FMC', 'WRB', 'GPC', 'EXPD', 'AAL', 'MAS', 'FDS',
        //     'HST', 'DOV', 'HAS', 'ROL', 'SNA', 'XYL', 'NDAQ', 'DRI', 'IFF', 'NWL',
        //     'PKG', 'CHD', 'GL', 'MCK', 'SWK', 'TEL', 'NCLH', 'DISH', 'LNT', 'ANET',
        //     'ALLE', 'WAT', 'CBOE', 'PFG', 'AOS', 'TYL', 'JKHY', 'UHS', 'VMC', 'GRMN',
        //     'CDW', 'AKAM', 'TECH', 'HOLX', 'MHK', 'HSY', 'MLM', 'MOH', 'NDSN', 'RE',
        //     'CHRW', 'BF.B', 'PNW', 'MKL', 'WHR', 'TRMB', 'L', 'NEM', 'NOV', 'FTNT',
        //     'DXCM', 'ABMD', 'VRSK', 'ZBRA', 'CDNS', 'IDXX', 'TTC', 'SWKS', 'CHTR',
        //     'SNPS', 'QRVO', 'NOW', 'POOL', 'CTLT', 'ALB', 'CNC', 'ALXN', 'BLL',
        //     'RCL', 'AJG', 'TAP', 'ODFL', 'WRK', 'CRL', 'NWSA', 'XRAY', 'CERN', 'ALLE',
        //     'DRE', 'CF', 'HRB', 'NRG', 'JBHT', 'TPR', 'LDOS', 'JWN', 'WOOF', 'CPRT',
        //     'LUMN', 'FOXA', 'CPRI', 'RIG', 'JBL', 'PENN', 'CCL', 'MOS', 'FL', 'BEN',
        //     'GWW', 'IT', 'UNM', 'LNC', 'COHR', 'IRM', 'DVA', 'FRC', 'ROL', 'UAA',
        //     'UDR', 'GNRC', 'SBH', 'DXC', 'LKQ', 'TSN', 'JNPR', 'CHH', 'HBI', 'TDC'
        // ];

        // $symbols = [
        //     'AAPL', 'MSFT', 'BTCUSD', 'XRPUSD'
        // ];

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
        
        

        $apiKey = 'ExYlr0LoPC6GqCmzuScjwq79Fn4Krx77';
        $batchSize = 100;
    
        $symbolChunks = array_chunk($symbols, $batchSize);
    
        foreach ($symbolChunks as $chunk) {
            $symbolsString = implode(',', $chunk);
            $apiUrl = "https://financialmodelingprep.com/api/v3/quote/{$symbolsString}?apikey={$apiKey}";
    
            try {
                $response = Http::get($apiUrl);
    
                if ($response->failed()) {
                    $this->command->error("Failed to fetch data for symbols: {$symbolsString}");
                    continue;
                }
    
                $stocks = $response->json();
                if (empty($stocks)) {
                    $this->command->warn("No data returned for symbols: {$symbolsString}");
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
                        'type' => 'stocks',
                        'status' => 'active',
                        'tradeable' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
    
                DB::table('assets')->upsert($stocksData, ['symbol'], array_keys($stocksData[0]));
                $this->command->info("Successfully inserted/updated stocks for batch: {$symbolsString}");
            } catch (\Exception $e) {
                $this->command->error("Error processing symbols: {$symbolsString} - " . $e->getMessage());
                Log::error("Error in StockSeeder", ['symbols' => $symbolsString, 'error' => $e->getMessage()]);
            }
        }
    
        $this->command->info("Stock seeding completed.");
    }
}
