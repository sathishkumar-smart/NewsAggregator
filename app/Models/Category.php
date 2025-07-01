<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Technology"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-29T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-29T12:00:00Z")
 * )
 */
class Category extends Model
{
    use HasFactory;


    protected $fillable = ['name'];

    /**
     * Get all articles that belong to this category.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * The users who have this category in their preferences.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
