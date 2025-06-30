<?php

namespace App\Repositories\Contracts;

use App\Models\UserPreference;

interface UserPreferenceRepositoryInterface
{
    /**
     * Create or update the preferences for a user.
     *
     * @param array $data
     * @return UserPreference
     */
    public function updateOrCreate(array $data): UserPreference;

    /**
     * Get preferences for a given user.
     *
     * @param int $userId
     * @return UserPreference|null
     */
    public function getByUserId(int $userId): ?UserPreference;
}