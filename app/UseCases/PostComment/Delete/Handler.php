<?php

declare(strict_types=1);

namespace App\UseCases\PostComment\Delete;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Exception;

class Handler
{
    /**
     * @param Command $command
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $user = User::find($command->userId);

        if (!$user) {
            throw new Exception('Failed to find user for comment');
        }

        $post = Post::find($command->postId);

        if (!$post) {
            throw new Exception('Failed to find post');
        }

        $comment = PostComment::find($command->commentId);

        if (!$comment) {
            throw new Exception('Failed to find comment');
        }

        if ($comment->user_id != $command->userId && !$user->hasRole('admin')) {
            throw new Exception('Failed to delete comment (not owner)');
        }

        if ($command->postId != $comment->post_id) {
            throw new Exception('Failed to delete comment (wrong combination "post-comment")');
        }

        $comment->delete();
    }
}
