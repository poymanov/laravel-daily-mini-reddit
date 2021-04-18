<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * @param int      $communityId
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllLatestByCommunityId(int $communityId, int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.community_posts');

        return Post::latest()->where('community_id', $communityId)->paginate($perPage);
    }
}
