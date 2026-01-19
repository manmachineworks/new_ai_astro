<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'nullable|string|max:255',
        ];
    }
}
