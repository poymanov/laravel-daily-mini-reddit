<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PostComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostCommentService
{
    /**
     * Получение комментариев публикации
     *
     * @param int      $postId
     * @param int|null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getAllByPostId(int $postId, int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? config('pagination.post_comments');

        return PostComment::wherePostId($postId)->orderBy('created_at')->paginate($perPage);
    }
}
