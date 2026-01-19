<?php

namespace App\Http\Requests\Astrologer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentNotesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAstrologer();
    }

    public function rules(): array
    {
        return [
            'notes' => 'required|string|max:500',
        ];
    }
}
