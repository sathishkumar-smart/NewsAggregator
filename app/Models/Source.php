<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Source",
 *     title="Source",
 *     description="News Source model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="BBC News"),
 *     @OA\Property(property="source_id", type="string", example="bbc-news"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-29T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-29T12:00:00Z")
 * )
 */
class Source extends Model
{
    use HasFactory;


    protected $fillable = ['name', 'source_id'];

    /**
     * Get all articles from this source.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }


    /**
     * The users who have this source in their preferences.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
