<?php

namespace App\Http\Controllers\User;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;
use App\Spatie\QueryBuilder\IncludeSelectFields;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $articles = QueryBuilder::for(Article::where('status', 'enabled')) // Fetch only active articles
            ->allowedFields(['id', 'title', 'slug', 'content', 'image', 'created_at'])
            ->allowedFilters([
                'title', 
                'slug', 
                AllowedFilter::scope('creation_date'),
            ])
            ->defaultSort('-created_at') // Sort by newest first
            ->allowedSorts(['title', 'created_at'])
            ->paginate((int) $request->per_page)
            ->withQueryString();

        return ResponseBuilder::asSuccess()
            ->withMessage('Active articles fetched successfully')
            ->withData(['articles' => $articles])
            ->build();
    }

    public function show($id): Response
    {
        $article = QueryBuilder::for(Article::where('id', $id))
            ->allowedFields(['id', 'title', 'slug', 'content', 'image', 'created_at'])
            ->allowedIncludes([
                AllowedInclude::custom('author', new IncludeSelectFields([
                    'id',
                    'name',
                    'email',
                ])),
            ])
            ->firstOrFail();

        return ResponseBuilder::asSuccess()
            ->withMessage('Article fetched successfully')
            ->withData(['article' => $article])
            ->build();
    }
}
