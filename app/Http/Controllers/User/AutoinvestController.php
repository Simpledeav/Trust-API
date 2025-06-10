<?php

namespace App\Http\Controllers\User;

use App\Models\AutoPlan;
use Illuminate\Http\Request;
use App\Models\AutoPlanInvestment;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Services\User\AutoPlanService;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\Http\Requests\User\CreateAutoPlanInvestmentRequest;

class AutoinvestController extends Controller
{
    public function __construct(private AutoPlanService $autoPlanService)
    {
    }
    
    public function index(Request $request): Response
    {
        $plans = QueryBuilder::for(AutoPlan::where('status', 'active')) // Fetch only active plans
            ->allowedFields([
                'id', 'name', 'min_invest', 'max_invest', 'win_rate',
                'duration', 'milestone', 'aum', 'returns', 'img', 'created_at'
            ])
            ->allowedFilters([
                'name',
                'duration',
                'milestone',
                'aum',
                'type',
                AllowedFilter::scope('creation_date'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->paginate((int) $request->per_page ?: 10)
            ->withQueryString();
    
        return ResponseBuilder::asSuccess()
            ->withMessage('Active auto plans fetched successfully')
            ->withData(['plans' => $plans])
            ->build();
    }   
    
    public function store(CreateAutoPlanInvestmentRequest $request): Response
    {
        try {
            $investment = $this->autoPlanService->startInvestment(
                $request->user(),
                $request->validated()
            );

            return ResponseBuilder::asSuccess()
                ->withMessage('Auto Investing created successfully')
                ->withData(['investment' => $investment])
                ->build();

        } catch (\Exception $e) {
            return ResponseBuilder::asError(500)
                ->withMessage($e->getMessage() ?: 'Unable to process auto investments.')
                ->build();
        }
    }

    public function investment(Request $request): Response
    {
        $investment = QueryBuilder::for(
                    AutoPlanInvestment::where('user_id', $request->user()->id)
                    ->with(['plan', 'positions.asset']) // Load positions and their assets
                    ->orderBy('created_at', 'desc')
                )
            ->allowedFields([
                'id', 'name',
            ])
            ->allowedFilters([
                'aum',
                AllowedFilter::scope('creation_date'),
            ])
            ->defaultSort('-created_at')
            ->allowedSorts(['name', 'created_at'])
            ->paginate((int) $request->per_page ?: 10)
            ->withQueryString();
    
        // Transform each investment to include P/L calculations
        $transformedData = $investment->getCollection()->map(function ($investment) {
            $totalProfit = 0;
            
            // Calculate total P/L from all positions
            foreach ($investment->positions as $position) {
                $assetPrice = $position->asset->price;
                $quantity = $position->quantity;
                $extra = $position->extra;
                $leverage = abs($position->leverage ?? 1);
                
                $singleProfit = ($assetPrice * $quantity) - $position->amount;
                $profit = ($singleProfit * $leverage) + $extra;
                $totalProfit += $profit;
            }
    
            // Add computed fields to the investment
            $investment->total_return = number_format($totalProfit, 2);
            $investment->percentage_return = $investment->amount != 0 
                ? number_format(($totalProfit / $investment->amount) * 100, 2)
                : 0;

            // Hide the positions data from the response
            $investment->makeHidden('positions');
    
            return $investment;
        });
    
        // Replace the original collection with the transformed one
        $investment->setCollection($transformedData);
    
        return ResponseBuilder::asSuccess()
            ->withMessage('Investment fetched successfully')
            ->withData(['investment' => $investment])
            ->build();
    }
}
