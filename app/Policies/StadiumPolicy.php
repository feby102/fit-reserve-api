<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Stadium;
use Illuminate\Auth\Access\Response;

class StadiumPolicy
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
    public function view(User $user, Stadium $Stadium): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role==='vendor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Stadium $stadium): bool
    {
       return $user->id === $stadium->vendor_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Stadium $stadium): bool
    {
        return $user->id === $stadium->user_id;
    }

     public function approve(User $user, Stadium $stadium): bool
    {
        return $user->id === $stadium->user_id;
    }

     public function reject(User $user, Stadium $stadium): bool
    {
        return $user->id === $stadium->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Stadium $Stadium): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Stadium $Stadium): bool
    {
        return false;
    }
}
