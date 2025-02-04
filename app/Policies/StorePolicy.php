<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isCustomer() && !$user->store && $user->hasVerifiedEmail();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Store $store)
    {
        return  $user->isVendor() && $user->id === $store->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Store $store)
    {
        return ($user->isAdministrator || $user->isSuperAdministrator()) ||
            ($user->isVendor() && $user->id === $store->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Store $store)
    {
        return  $user->isSuperAdministrator() || $user->isAdministrator();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Store $store)
    {
        return  $user->isSuperAdministrator() || $user->isAdministrator();
    }
}
