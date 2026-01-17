<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AstrologerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user') ?: $this->user),
            'bio' => $this->bio,
            'skills' => $this->skills,
            'languages' => $this->languages,
            'experience_years' => $this->experience_years,
            'call_per_minute' => $this->call_per_minute,
            'chat_per_session' => $this->chat_per_session,
            'is_call_enabled' => $this->is_call_enabled,
            'is_chat_enabled' => $this->is_chat_enabled,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'visibility' => $this->visibility,
        ];
    }
}
