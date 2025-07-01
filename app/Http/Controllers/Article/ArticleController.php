<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description="Endpoints for managing articles"
 * )
 */
class ArticleController extends Controller
{
    public function __construct(protected ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="List articles with filters and pagination",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="source_id",
     *         in="query",
     *         description="Filter by source ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="author_id",
     *         in="query",
     *         description="Filter by author ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with list of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong while fetching articles.")
     *         )
     *     )
     * )
     */
    public function index(FilterArticleRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $cacheKey = 'articles_index_' . md5(json_encode($filters));

            $articles = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filters) {
                return $this->articleService->listArticles($filters);
            });

            return response()->json([
                'success' => true,
                'data' => $articles,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'api/articles');

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching articles.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a specific article by ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Article not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong while fetching the article.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $cacheKey = 'article_' . $id;

            $article = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
                return $this->articleService->getArticleById($id);
            });

            if (!$article) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'data' => $article,
            ]);
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'api/articles');

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching the article.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

