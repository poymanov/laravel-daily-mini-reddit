<?php

declare(strict_types=1);

namespace App\UseCases\PostVote\Create;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Handler
{
    public function handle(Command $command): void
    {
        $community = Community::find($command->communityId);

        if (!$community) {
            throw new Exception('Failed to find community for post');
        }

        $user = User::find($command->userId);

        if (!$user) {
            throw new Exception('Failed to find user for vote');
        }

        $post = Post::find($command->postId);

        if (!$post) {
            throw new Exception('Failed to find post');
        }

        if ($post->user_id == $command->userId) {
            throw new Exception('This user cannot vote for this post (owner)');
        }

        if (!in_array($command->vote, [1, -1])) {
            throw new Exception('Wrong vote parameter');
        }

        $postVote = PostVote::wherePostId($command->postId)->whereUserId($command->userId)->first();

        if (!$postVote) {
            $postVote          = new PostVote();
            $postVote->post_id = $command->postId;
            $postVote->user_id = $command->userId;
        }

        $postVote->vote = $command->vote;

        if (!$postVote->save()) {
            throw new Exception('Failed to vote for post');
        }
    }
}
