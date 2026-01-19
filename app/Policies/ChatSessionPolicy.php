<?php

namespace App\Policies;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatSessionPolicy
{
    public function view(User $user, ChatSession $session): Response
    {
        return $user->astrologer && $session->astrologer_id === $user->astrologer->id
            ? Response::allow()
            : Response::deny('You cannot access this chat.');
    }

    public function update(User $user, ChatSession $session): Response
    {
        return $this->view($user, $session);
    }
}
