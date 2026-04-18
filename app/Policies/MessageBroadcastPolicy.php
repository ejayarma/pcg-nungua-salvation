<?php

namespace App\Policies;

use App\Enums\MessageBroadcastStatusEnum;
use App\Models\MessageBroadcast;
use App\Models\User;
use Illuminate\Support\Facades\Date;

class MessageBroadcastPolicy
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
    public function view(User $user, MessageBroadcast $model): bool
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
     * Only allow updates if status is PENDING and scheduled_at hasn't passed.
     */
    public function update(User $user, MessageBroadcast $model): bool
    {
        // Allow update only if pending and scheduled time hasn't elapsed
        return $model->status === MessageBroadcastStatusEnum::PENDING && Date::make($model->scheduled_at) > now();
    }

    /**
     * Determine whether the user can delete the model.
     * Only allow deletion if status is PENDING and scheduled_at hasn't passed.
     */
    public function delete(User $user, MessageBroadcast $model): bool
    {
        // Allow deletion only if pending and scheduled time hasn't elapsed
        return $model->status === MessageBroadcastStatusEnum::PENDING && Date::make($model->scheduled_at) > now();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MessageBroadcast $model): bool
    {
        return $model->status === MessageBroadcastStatusEnum::PENDING && Date::make($model->scheduled_at) > now();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MessageBroadcast $model): bool
    {
        return $model->status === MessageBroadcastStatusEnum::PENDING && Date::make($model->scheduled_at) > now();
    }
}
