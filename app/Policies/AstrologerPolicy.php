<?php

namespace App\Policies;

use App\Models\Astrologer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AstrologerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAstrologer();
    }

    public function view(User $user, Astrologer $astrologer): Response
    {
        return $user->id === $astrologer->user_id
            ? Response::allow()
            : Response::deny('You may only view your own astrologer profile.');
    }

    public function update(User $user, Astrologer $astrologer): Response
    {
        return $this->view($user, $astrologer);
    }
}
