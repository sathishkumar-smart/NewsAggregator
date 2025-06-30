<?php

namespace App\Repositories;

use App\Models\UserPreference;
use App\Repositories\Contracts\UserPreferenceRepositoryInterface;

class UserPreferenceRepository implements UserPreferenceRepositoryInterface
{
    /**
     * Update or create user preferences.
     *
     * @param array $data
     * @return UserPreference
     */
    public function updateOrCreate(array $data): UserPreference
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $data['user_id']],
            [
                'sources' => $data['sources'] ?? [],
                'categories' => $data['categories'] ?? [],
                'authors' => $data['authors'] ?? [],
            ]
        );
    }

    /**
     * Retrieve preferences by user ID.
     *
     * @param int $userId
     * @return UserPreference|null
     */
    public function getByUserId(int $userId): ?UserPreference
    {
        return UserPreference::where('user_id', $userId)->first();
    }
}
