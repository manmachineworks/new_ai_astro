<?php

namespace App\Policies;

use App\Models\CallLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CallLogPolicy
{
    public function view(User $user, CallLog $callLog): Response
    {
        return $user->astrologer && $callLog->astrologer_id === $user->astrologer->id
            ? Response::allow()
            : Response::deny('Not authorized to view this call log.');
    }
}
