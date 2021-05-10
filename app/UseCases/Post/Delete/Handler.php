<?php

declare(strict_types=1);

namespace App\UseCases\Post\Delete;

use App\Models\Post;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\DB;

class Handler
{
    /** @var UserService */
    private UserService $userService;

    /**
     * Handler constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

        if ($post->user_id != $command->userId && !$this->userService->isAdmin($user->id)) {
            throw new Exception('This user cannot delete this post (not owner)');
        }

        DB::transaction(function () use ($post) {
            $post->delete();
            $post->comments()->delete();
        });
    }
}
