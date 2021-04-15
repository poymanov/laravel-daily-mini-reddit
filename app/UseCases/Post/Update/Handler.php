<?php

declare(strict_types=1);

namespace App\UseCases\Post\Update;

use App\Models\Community;
use App\Models\Post;
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
        $community = Community::find($command->communityId);

        if (!$community) {
            throw new Exception('Failed to find community for post');
        }

        $user = User::find($command->userId);

        if (!$user) {
            throw new Exception('Failed to find user for post');
        }

        $post = Post::find($command->id);

        if (!$post) {
            throw new Exception('Failed to find post');
        }

        if ($post->user_id != $command->userId) {
            throw new Exception('This user cannot update this post (not owner)');
        }

        $post->title = $command->title;
        $post->text  = $command->text;
        $post->url   = $command->url;

        if (!$post->save()) {
            throw new Exception('Failed to update post');
        }
    }
}
