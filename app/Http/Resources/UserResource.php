<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwner = $user && $user->id === $this->id;
        $isAdmin = $user && $user->hasAnyRole(['Super Admin', 'Admin', 'Finance Admin', 'Support Admin', 'Ops Admin']);
        $isAstrologer = $user && $user->hasRole('Astrologer');

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'avatar_url' => $this->avatar_url,
            'created_at' => $this->created_at,
        ];

        if ($isOwner || $isAdmin) {
            $data['phone'] = $this->phone;
            $data['email'] = $this->email;
        } elseif ($isAstrologer) {
            $data['phone'] = null;
            $data['email'] = null;
        } else {
            // Masked versions if needed for UI, or omit entirely
            $data['phone'] = $this->mask($this->phone, 2, 2);
            $data['email'] = $this->maskEmail($this->email);
        }

        return $data;
    }

    protected function mask($string, $start, $end)
    {
        if (!$string)
            return null;
        $len = strlen($string);
        if ($len <= $start + $end)
            return str_repeat('*', $len);
        return substr($string, 0, $start) . str_repeat('*', $len - $start - $end) . substr($string, -$end);
    }

    protected function maskEmail($email)
    {
        if (!$email)
            return null;
        $parts = explode('@', $email);
        if (count($parts) < 2)
            return str_repeat('*', strlen($email));
        return $this->mask($parts[0], 1, 1) . '@' . $parts[1];
    }
}
