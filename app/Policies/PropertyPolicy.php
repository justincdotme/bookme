<?php

namespace App\Policies;

use App\Core\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create properties.
     *
     * @param  \App\Core\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create properties.
     *
     * @param  \App\Core\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->isAdmin();
    }
}