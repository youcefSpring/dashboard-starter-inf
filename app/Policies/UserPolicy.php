<?php

namespace App\Policies;

use App\Models\User;

/**
 * Who may manage accounts on the Users page.
 *
 * The starter has no roles, so every signed-in user is treated as an admin.
 * Tighten these checks (e.g. `return $user->is_admin;`) once you add roles.
 */
class UserPolicy
{
    /**
     * Determine whether the user can list users.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the given user.
     */
    public function update(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the given user. Nobody can delete their own
     * account from here; that goes through Settings, which asks for the password.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isNot($model);
    }
}
