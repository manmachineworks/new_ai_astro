<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'call_per_minute' => 'required|numeric|min:0',
            'chat_price' => 'required|numeric|min:0',
            'ai_chat_price' => 'required|numeric|min:0',
        ];
    }
}
