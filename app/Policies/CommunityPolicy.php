<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Community  $community
     * @return mixed
     */
    public function view(User $user, Community $community)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Community  $community
     * @return mixed
     */
    public function update(User $user, Community $community)
    {
        return $user->id == $community->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Community  $community
     * @return mixed
     */
    public function delete(User $user, Community $community)
    {
        return $this->isUserAdmin($user) || $user->id == $community->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Community  $community
     * @return mixed
     */
    public function restore(User $user, Community $community)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Community  $community
     * @return mixed
     */
    public function forceDelete(User $user, Community $community)
    {
        //
    }

    /**
     * Проверка, является ли пользователь администратором
     *
     * @param User $user
     * @return bool
     */
    private function isUserAdmin(User $user): bool
    {
        return $user->hasRole(RoleEnum::ADMIN);
    }
}
