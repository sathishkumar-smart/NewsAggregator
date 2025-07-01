<?php

namespace App\Services;

use App\Repositories\UserPreferenceRepository;
use Cache;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserPreferenceService
{
    protected UserPreferenceRepository $preferenceRepo;

    public function __construct(UserPreferenceRepository $preferenceRepo)
    {
        $this->preferenceRepo = $preferenceRepo;
    }

    /**
     * Store or update user preferences.
     *
     * @param array $data
     * @return \App\Models\UserPreference
     */
    public function storeOrUpdate(array $data)
    {
        // Attach current user's ID to the preference data
        $data['user_id'] = Auth::id();

        return $this->preferenceRepo->updateOrCreate($data);
    }

    /**
     * Get the preferences for the current user.
     *
     * @return \App\Models\UserPreference|array
     */
    public function getUserPreferences()
    {
        $user = Auth::user()->load(['sources', 'categories', 'authors']);

        return [
            'sources' => $user->sources,
            'categories' => $user->categories,
            'authors' => $user->authors,
        ];
    }

    /**
     * Fetch personalized articles based on preferences.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPersonalizedFeed()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Define a unique cache key based on the user's ID
        $cacheKey = 'user_feed_' . $user->id;

        try {
            // Attempt to retrieve the personalized feed from cache.
            return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {

                // Fetch user preferences (IDs of sources, categories, and authors)
                $preferences = [
                    'sources' => $user->sources()->pluck('id')->all(),       // e.g., [1, 2]
                    'categories' => $user->categories()->pluck('id')->all(), // e.g., [3, 4]
                    'authors' => $user->authors()->pluck('id')->all(),       // e.g., [5, 6]
                ];

                // Start building the article query
                $query = \App\Models\Article::query();

                // Filter articles by preferred sources, if any
                if (!empty($preferences['sources'])) {
                    $query->whereIn('source_id', $preferences['sources']);
                }

                // Filter articles by preferred categories, if any
                if (!empty($preferences['categories'])) {
                    $query->whereIn('category_id', $preferences['categories']);
                }

                // Filter articles by preferred authors, if any
                if (!empty($preferences['authors'])) {
                    $query->whereIn('author_id', $preferences['authors']);
                }

                // Return the final result: latest articles, paginated (10 per page)
                return $query->latest('published_at')->paginate(10);
            });
        } catch (Throwable $th) {
            storeCustomLogsThrowable($th, 'services/userpreference');
        }
    }
}
