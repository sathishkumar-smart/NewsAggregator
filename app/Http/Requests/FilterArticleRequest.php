<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handle and validate article filter input.
 *
 * @OA\Schema(
 *     schema="FilterArticleRequest",
 *     @OA\Property(property="keyword", type="string", example="AI"),
 *     @OA\Property(property="source_id", type="integer", example=2),
 *     @OA\Property(property="category_id", type="integer", example=2),
 *     @OA\Property(property="date", type="string", format="date", example="2025-02-11"),
 *     @OA\Property(property="per_page", type="integer", example=10),
 * )
 */
class FilterArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string',
            'source_id' => 'nullable|integer|exists:sources,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'date' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
