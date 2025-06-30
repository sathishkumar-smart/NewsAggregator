<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserPreferenceRequest;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;

class UserPreferenceController extends Controller
{
    protected UserPreferenceService $service;

    public function __construct(UserPreferenceService $service)
    {
        $this->middleware('auth:sanctum'); // Protect routes
        $this->service = $service;
    }

    /**
     * Store or update user preferences.
     */
    public function store(StoreUserPreferenceRequest $request): JsonResponse
    {
        $preferences = $this->service->storeOrUpdate($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Preferences saved successfully.',
            'data' => $preferences,
        ]);
    }

    /**
     * Get current user preferences.
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
     * Fetch personalized article feed based on preferences.
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
