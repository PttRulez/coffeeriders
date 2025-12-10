<?php

namespace App\Policies;

use App\Models\CyclingActivity;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CyclingActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CyclingActivity $cyclingActivity): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CyclingActivity $cyclingActivity): Response
    {
        if ($cyclingActivity->user_id !== $user->id) {
            return Response::denyWithStatus(404);
        }

        if ($cyclingActivity->starts_at->lte(now()->addHours(6))) {
            return Response::deny('Бронь можно изменить не позднее чем за 6 часов до начала занятия.');
        }

        return Response::allow();
    }
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CyclingActivity $cyclingActivity): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CyclingActivity $cyclingActivity): bool
    {
        return false;
    }
    
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CyclingActivity $cyclingActivity): bool
    {
        return false;
    }
}
