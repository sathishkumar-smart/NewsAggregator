<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ensure authenticated routes handle auth
    }

    public function rules(): array
    {
        return [
            'sources' => 'nullable|array',
            'sources.*' => 'string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:255',
            'authors' => 'nullable|array',
            'authors.*' => 'string|max:255',
        ];
    }
}
