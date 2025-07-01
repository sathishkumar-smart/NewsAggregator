<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="UserPreference",
 *     title="User Preference",
 *     description="Stores a user's preferred sources, categories, and authors",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=42),
 *     @OA\Property(
 *         property="sources",
 *         type="array",
 *         @OA\Items(type="integer", example=1)
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="integer", example=3)
 *     ),
 *     @OA\Property(
 *         property="authors",
 *         type="array",
 *         @OA\Items(type="integer", example=5)
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-29T14:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-29T14:00:00Z")
 * )
 */
class UserPreference extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'sources', 'categories', 'authors'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sources' => 'array',
        'categories' => 'array',
        'authors' => 'array',
    ];

    /**
     * Get the user that owns the preferences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
