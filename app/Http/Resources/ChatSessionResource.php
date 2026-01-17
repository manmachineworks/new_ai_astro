<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatSessionResource extends JsonResource
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
            'astrologer' => new UserResource($this->whenLoaded('astrologer') ?: $this->astrologer),
            'conversation_id' => $this->conversation_id,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'pricing_mode' => $this->pricing_mode,
            'total_charged' => $this->total_charged,
            'firebase_chat_id' => $this->firebase_chat_id,
        ];
    }
}
