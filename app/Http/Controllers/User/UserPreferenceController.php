<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserPreferenceRequest;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="Manage user preferences and personalized article feeds"
 * )
 */
class UserPreferenceController extends Controller
{
    /**
     * The user preference service instance.
     *
     * @var UserPreferenceService
     */
    protected UserPreferenceService $service;

    /**
     * Constructor: applies authentication middleware and injects service.
     *
     * @param UserPreferenceService $service The service handling user preference logic.
     */
    public function __construct(UserPreferenceService $service)
    {
        $this->middleware('auth:sanctum'); // Protect routes with token-based auth
        $this->service = $service;
    }

    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     summary="Store or update user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="sources", type="array", @OA\Items(type="integer"), example={1,2,3}),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={4,5}),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="integer"), example={7})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Preferences saved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Something went wrong"
     *     )
     * )
     */
    public function store(StoreUserPreferenceRequest $request): JsonResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        try {
            // Log request for debugging/auditing
            storeCustomLogs(['storeapi' => $request->all()], 'api/userpreference');

            // Sync selected sources, categories, and authors to the user
            if (isset($data['sources'])) {
                $user->sources()->sync($data['sources']);
            }

            if (isset($data['categories'])) {
                $user->categories()->sync($data['categories']);
            }

            if (isset($data['authors'])) {
                $user->authors()->sync($data['authors']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferences saved successfully.',
                'data' => [
                    'sources' => $user->sources()->pluck('id'),
                    'categories' => $user->categories()->pluck('id'),
                    'authors' => $user->authors()->pluck('id'),
                ],
            ]);
        } catch (\Throwable $th) {
            // Log the exception and return a generic error response
            storeCustomLogsThrowable($th, 'api/userpreference');
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     summary="Get current user preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function show(): JsonResponse
    {
        $preferences = $this->service->getUserPreferences();

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/preferences/feed",
     *     summary="Get personalized article feed based on preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Personalized articles retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function personalizedFeed(): JsonResponse
    {
        $articles = $this->service->getPersonalizedFeed();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }
}
