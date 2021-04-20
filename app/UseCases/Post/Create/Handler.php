<?php

declare(strict_types=1);

namespace App\UseCases\Post\Create;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use App\Services\PostImageService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use Str;

class Handler
{
    /**
     * @param Command $command
     *
     * @throws \Throwable
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

        DB::transaction(function () use ($community, $user, $command) {
            $post               = new Post();
            $post->community_id = $community->id;
            $post->user_id      = $user->id;
            $post->title        = $command->title;
            $post->text         = $command->text;
            $post->url          = $command->url;

            if (!$post->save()) {
                throw new Exception('Failed to create post');
            }

            if ($command->image) {
                /** @var PostImageService $postImageService */
                $postImageService = App::make(PostImageService::class);

                $postImageService->storeUploadedFile($command->image, $post->id);
            }
        });
    }
}
