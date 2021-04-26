<?php

declare(strict_types=1);

namespace App\UseCases\PostComment\Create;

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
            throw new Exception('Failed to find user for post');
        }

        $post = Post::find($command->postId);

        if (!$post) {
            throw new Exception('Failed to find post');
        }

        $postComment          = new PostComment();
        $postComment->post_id = $command->postId;
        $postComment->user_id = $command->userId;
        $postComment->text    = $command->text;

        if (!$postComment->save()) {
            throw new Exception('Failed to create comment for post');
        }
    }
}
