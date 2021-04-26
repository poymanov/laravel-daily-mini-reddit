<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostComment\CreateRequest;
use App\Models\Community;
use App\Models\Post;
use Exception;
use Illuminate\Http\Response;
use App\UseCases\PostComment\Create;

class PostCommentController extends Controller
{
    /**
     * @param CreateRequest $request
     * @param Community     $community
     * @param Post          $post
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateRequest $request, Community $community, Post $post)
    {
        if ($community->id != $post->community_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        try {
            $command         = new Create\Command();
            $command->postId = $post->id;
            $command->userId = (int) auth()->id();
            $command->text   = $request->get('text');

            $handler = new Create\Handler();
            $handler->handle($command);

            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.success', 'Comment created');
        } catch (Exception $e) {
            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.error', 'Failed to create comment');
        }
    }
}
