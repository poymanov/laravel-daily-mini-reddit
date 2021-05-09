<?php

declare(strict_types=1);

namespace App\UseCases\Post\Delete;

use App\Enums\RoleEnum;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

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

        $post = Post::find($command->id);

        if (!$post) {
            throw new Exception('Failed to find post');
        }

        if ($post->user_id != $command->userId && !$user->hasRole(RoleEnum::ADMIN)) {
            throw new Exception('This user cannot delete this post (not owner)');
        }

        DB::transaction(function () use ($post) {
            $post->delete();
            $post->comments()->delete();
        });
    }
}
