<?php

declare(strict_types=1);

namespace App\UseCases\Post\Create;

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

        $post               = new Post();
        $post->community_id = $community->id;
        $post->user_id      = $user->id;
        $post->title        = $command->title;
        $post->text         = $command->text;
        $post->url          = $command->url;

        if (!$post->save()) {
            throw new Exception('Failed to create post');
        }
    }
}
