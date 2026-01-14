<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firebase_id_token' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
