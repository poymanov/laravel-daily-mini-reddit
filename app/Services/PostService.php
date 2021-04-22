<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PostService
{
    /**
     * @param int      $communityId
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllLatestByCommunityId(int $communityId, ?int $userId, int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.community_posts');

        $query = Post::latest();

        if ($userId) {
            $query = $this->modifyQueryWithUserVotes($query, $userId);
        }

        return $query->where('community_id', $communityId)->paginate($perPage);
    }

    /**
     * @param int|null $userId
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllLatest(?int $userId, int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.home_posts');

        $query = Post::latest();

        if ($userId) {
            $query = $this->modifyQueryWithUserVotes($query, $userId);
        }

        return $query->paginate($perPage);
    }

    /**
     * Получение публикации с данными по голосованиям пользователя
     *
     * @param int      $postId
     * @param int|null $userId
     *
     * @return Post|Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getPostWithUserVotes(int $postId, ?int $userId)
    {
        $query = Post::whereId($postId);

        if ($userId) {
            $query = $this->modifyQueryWithUserVotes($query, $userId);
        }

        return $query->first();
    }

    /**
     * @param Builder $query
     * @param int     $userId
     *
     * @return Builder
     */
    private function modifyQueryWithUserVotes(Builder $query, int $userId): Builder
    {
        return $query->withCount([
            'votes as current_user_like'    => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('vote', 1);
            },
            'votes as current_user_dislike' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('vote', -1);
            },
        ]);
    }
}
