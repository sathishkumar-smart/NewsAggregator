<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handle and validate user preference saving requests.
 *
 * @OA\Schema(
 *     schema="StoreUserPreferenceRequest",
 *     required={"sources", "categories", "authors"},
 *     @OA\Property(
 *         property="sources",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={1, 2, 3}
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={4, 5}
 *     ),
 *     @OA\Property(
 *         property="authors",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={6}
 *     )
 * )
 */
class StoreUserPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Ensure authenticated routes handle auth
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sources' => 'nullable|array',
            'sources.*' => 'exists:sources,id',

            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',

            'authors' => 'nullable|array',
            'authors.*' => 'exists:authors,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                !$this->has('sources') ||
                !$this->has('categories') ||
                !$this->has('authors')
            ) {
                $validator->errors()->add(
                    'preferences',
                    'At least any one of sources, categories, or authors must be provided.'
                );
            }
        });
    }
}
