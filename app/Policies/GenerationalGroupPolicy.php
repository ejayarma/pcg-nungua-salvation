<?php

namespace App\Policies;

use App\Models\GenerationalGroup;
use App\Models\User;

class GenerationalGroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GenerationalGroup $generationalGroup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GenerationalGroup $generationalGroup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GenerationalGroup $generationalGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore audit records for the model.
     */
    public function restoreAudit(User $user, GenerationalGroup $generationalGroup): bool
    {
        $allowedEmails = array_filter(array_map('trim', config('audit.restore.allowed_emails', [])));

        return $user->is_admin && in_array($user->email, $allowedEmails, true);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GenerationalGroup $generationalGroup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GenerationalGroup $generationalGroup): bool
    {
        return false;
    }
}
