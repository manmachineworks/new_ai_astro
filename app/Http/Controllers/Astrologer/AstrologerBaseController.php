<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use Illuminate\Support\Facades\Auth;

class AstrologerBaseController extends Controller
{
    protected function resolveAstrologer(): Astrologer
    {
        $user = Auth::user();
        if (!$user || !$user->astrologer) {
            return Astrologer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'public_id' => 'ASTRO-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                    'display_name' => $user->name,
                ]
            );
        }

        return $user->astrologer;
    }
}
