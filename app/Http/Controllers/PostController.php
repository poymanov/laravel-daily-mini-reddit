<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Community;
use App\Models\Post;
use App\Services\PostCommentService;
use App\Services\PostService;
use App\Services\UserService;
use App\UseCases\Post\Create;
use App\UseCases\Post\Update;
use App\UseCases\Post\Delete;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Throwable;

class PostController extends Controller
{
    /** @var PostService */
    private PostService $postService;

    /** @var PostCommentService */
    private PostCommentService $postCommentService;

    /** @var UserService */
    private UserService $userService;

    /**
     * @param PostService $postService
     * @param PostCommentService $postCommentService
     * @param UserService $userService
     */
    public function __construct(PostService $postService, PostCommentService $postCommentService, UserService $userService)
    {
        $this->postService = $postService;
        $this->postCommentService = $postCommentService;
        $this->userService = $userService;
    }

    /**
     * @param Community $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Community $community)
    {
        return view('post.create', compact('community'));
    }

    /**
     * @param CreateRequest $request
     * @param Community     $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateRequest $request, Community $community)
    {
        /** @var UploadedFile $image */
        $image = $request->file('image');

        $command              = new Create\Command();
        $command->communityId = $community->id;
        $command->userId      = (int) auth()->id();
        $command->title       = $request->get('title');
        $command->text        = $request->get('text');
        $command->url         = $request->get('url');
        $command->image       = $image;

        try {
            $handler = new Create\Handler();
            $handler->handle($command);

            return redirect(route('community.index'))->with('alert.success', 'Post created');
        } catch (Throwable $e) {
            return redirect(route('community.index'))->with('alert.error', 'Failed to create post');
        }
    }

    /**
     * @param Community $community
     * @param Post      $post
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Community $community, Post $post)
    {
        if ($community->id != $post->community_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $post);

        return view('post.edit', compact('community', 'post'));
    }

    /**
     * @param UpdateRequest $request
     * @param Community     $community
     * @param Post          $post
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, Community $community, Post $post)
    {
        if ($community->id != $post->community_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('update', $post);

        /** @var UploadedFile $image */
        $image = $request->file('image');

        $command              = new Update\Command();
        $command->id          = $post->id;
        $command->communityId = $community->id;
        $command->userId      = (int) auth()->id();
        $command->title       = $request->get('title');
        $command->text        = $request->get('text');
        $command->url         = $request->get('url');
        $command->image       = $image;
        $command->deleteImage = $request->has('delete_image');

        try {
            $handler = new Update\Handler();
            $handler->handle($command);

            return redirect(route('community.index'))->with('alert.success', 'Post updated');
        } catch (Throwable $e) {
            return redirect(route('community.index'))->with('alert.error', 'Failed to update post');
        }
    }

    /**
     * @param Community $community
     * @param Post      $post
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Community $community, Post $post)
    {
        if ($community->id != $post->community_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->authorize('delete', $post);

        $command         = new Delete\Command();
        $command->id     = $post->id;
        $command->userId = (int) auth()->id();

        try {
            $handler = new Delete\Handler($this->userService);
            $handler->handle($command);

            return redirect(route('community.index'))->with('alert.success', 'Post deleted');
        } catch (Throwable $e) {
            return redirect(route('community.index'))->with('alert.error', 'Failed to delete post');
        }
    }

    /**
     * @param Community $community
     * @param Post      $post
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Community $community, Post $post)
    {
        if ($community->id != $post->community_id) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $comments = $this->postCommentService->getAllByPostId($post->id);
        $post     = $this->postService->getPostWithUserVotes($post->id, (int) auth()->id());

        return view('post.show', compact('community', 'post', 'comments'));
    }
}
