<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallSessionResource extends JsonResource
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
            'astrologer_profile' => new AstrologerProfileResource($this->whenLoaded('astrologerProfile') ?: $this->astrologerProfile),
            'status' => $this->status,
            'started_at_utc' => $this->started_at_utc,
            'connected_at_utc' => $this->connected_at_utc,
            'ended_at_utc' => $this->ended_at_utc,
            'duration_seconds' => $this->duration_seconds,
            'billable_minutes' => $this->billable_minutes,
            'gross_amount' => $this->gross_amount,
            'provider' => $this->provider,
        ];
    }
}
