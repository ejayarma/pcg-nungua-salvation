<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is_admin && $user->email == $model->email;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is_admin && $user->email == $model->email;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->is_admin && $user->email == $model->email;
    }

    /**
     * Determine whether the user can restore audit records for the model.
     */
    public function restoreAudit(User $user, User $model): bool
    {
        $allowedEmails = array_filter(array_map('trim', config('audit.restore.allowed_emails', [])));

        return $user->is_admin && in_array($user->email, $allowedEmails, true);
    }
}
