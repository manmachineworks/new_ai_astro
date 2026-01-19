<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class UploadVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'verification_status' => 'required|in:pending,approved,rejected',
            'verification_remark' => 'nullable|string|max:500',
            'document' => 'nullable|file|mimes:jpg,png,pdf|max:5120',
        ];
    }
}
