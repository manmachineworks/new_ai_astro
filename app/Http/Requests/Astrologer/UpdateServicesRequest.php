<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'call_enabled' => 'required|boolean',
            'chat_enabled' => 'required|boolean',
            'sms_enabled' => 'required|boolean',
            'online_status' => 'required|in:online,offline',
        ];
    }
}
