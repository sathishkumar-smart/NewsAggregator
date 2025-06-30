<?php

namespace App\Services;

use App\Repositories\UserPreferenceRepository;
use Illuminate\Support\Facades\Auth;

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
     * @return \App\Models\UserPreference|null
     */
    public function getUserPreferences()
    {
        return $this->preferenceRepo->getByUserId(Auth::id());
    }

    /**
     * Fetch personalized articles based on preferences.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPersonalizedFeed()
    {
        $preferences = $this->getUserPreferences();

        $query = \App\Models\Article::query();

        if ($preferences) {
            if (!empty($preferences->sources)) {
                $query->whereHas('source', function ($q) use ($preferences) {
                    $q->whereIn('name', $preferences->sources);
                });
            }

            if (!empty($preferences->categories)) {
                $query->whereIn('category', $preferences->categories);
            }

            if (!empty($preferences->authors)) {
                $query->whereHas('author', function ($q) use ($preferences) {
                    $q->whereIn('name', $preferences->authors);
                });
            }
        }

        return $query->latest('published_at')->paginate(10);
    }
}
