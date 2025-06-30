<?php

namespace App\Http\Controller\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(protected ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * List articles with filters & pagination
     */
    public function index(FilterArticleRequest $request): JsonResponse
    {
        $articles = $this->articleService->listArticles($request->validated());

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get article by ID
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->articleService->getArticleById($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }
}
