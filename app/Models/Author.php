<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Author",
 *     title="Author",
 *     description="Author model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-29T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-29T12:34:56Z")
 * )
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all articles written by this author.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * The users who have this author in their preferences.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
