<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1200',
            'languages' => 'nullable|array',
            'languages.*' => 'string|max:32',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:64',
            'profile_photo_url' => 'nullable|url',
        ];
    }
}
