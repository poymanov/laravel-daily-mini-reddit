<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostComment\CreateRequest;
use App\Http\Requests\PostComment\UpdateRequest;
use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Response;
use App\UseCases\PostComment\Create;
use App\UseCases\PostComment\Update;
use App\UseCases\PostComment\Delete;

class PostCommentController extends Controller
{
    /** @var UserService */
    private UserService $userService;

    /**
     * PostCommentController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

    /**
     * @param Community   $community
     * @param Post        $post
     * @param PostComment $comment
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Community $community, Post $post, PostComment $comment)
    {
        if ($community->id != $post->community_id || $post->id != $comment->post_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $comment);

        return view('post.edit_comment', compact('community', 'post', 'comment'));
    }

    /**
     * @param UpdateRequest $request
     * @param Community     $community
     * @param Post          $post
     * @param PostComment   $comment
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, Community $community, Post $post, PostComment $comment)
    {
        if ($community->id != $post->community_id || $post->id != $comment->post_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $comment);

        try {
            $command            = new Update\Command();
            $command->postId    = $post->id;
            $command->commentId = $comment->id;
            $command->userId    = (int) auth()->id();
            $command->text      = $request->get('text');

            $handler = new Update\Handler();
            $handler->handle($command);

            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.success', 'Comment updated');
        } catch (Exception $e) {
            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.error', 'Failed to updated comment');
        }
    }

    /**
     * @param Community $community
     * @param Post $post
     * @param PostComment $comment
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Community $community, Post $post, PostComment $comment)
    {
        if ($community->id != $post->community_id || $post->id != $comment->post_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $comment);

        try {
            $command            = new Delete\Command();
            $command->postId    = $post->id;
            $command->commentId = $comment->id;
            $command->userId    = (int) auth()->id();

            $handler = new Delete\Handler($this->userService);
            $handler->handle($command);

            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.success', 'Comment deleted');
        } catch (Exception $e) {
            return redirect(route('communities.posts.show', [$community, $post]))->with('alert.error', 'Failed to delete comment');
        }
    }
}
