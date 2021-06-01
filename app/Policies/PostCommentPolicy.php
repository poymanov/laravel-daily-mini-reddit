<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\PostComment $postComment
     *
     * @return mixed
     */
    public function view(User $user, PostComment $postComment)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\PostComment $postComment
     *
     * @return mixed
     */
    public function update(User $user, PostComment $postComment)
    {
        return $user->id == $postComment->user_id && ($postComment->created_at && $postComment->created_at->diff(now())->days < 1);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\PostComment $postComment
     *
     * @return mixed
     */
    public function delete(User $user, PostComment $postComment)
    {
        return $this->isUserAdmin($user) ||
            ($user->id == $postComment->user_id && ($postComment->created_at && $postComment->created_at->diff(now())->days < 1));
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\PostComment $postComment
     *
     * @return mixed
     */
    public function restore(User $user, PostComment $postComment)
    {
        //
    }

    /**
     * @param User        $user
     * @param PostComment $postComment
     *
     * @return bool
     */
    public function report(User $user, PostComment $postComment)
    {
        return $user->hasVerifiedEmail() && $user->id != $postComment->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User        $user
     * @param \App\Models\PostComment $postComment
     *
     * @return mixed
     */
    public function forceDelete(User $user, PostComment $postComment)
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
