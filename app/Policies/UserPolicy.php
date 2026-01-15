<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_users');
    }

    public function update(User $user, User $target): bool
    {
        return $user->can('edit_users') && $user->id !== $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->can('delete_users') && $user->id !== $target->id;
    }

    public function block(User $user, User $target): bool
    {
        return $user->can('block_users') && $user->id !== $target->id;
    }

    public function manageRoles(User $user): bool
    {
        return $user->can('manage_roles_permissions');
    }
}
