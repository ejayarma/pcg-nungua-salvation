<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
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
    public function view(User $user, Member $member): bool
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
    public function update(User $user, Member $member): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Member $member): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore audit records for the model.
     */
    public function restoreAudit(User $user, Member $member): bool
    {
        $allowedEmails = array_filter(array_map('trim', config('audit.restore.allowed_emails', [])));

        return $user->is_admin && in_array($user->email, $allowedEmails, true);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Member $member): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Member $member): bool
    {
        return false;
    }
}
