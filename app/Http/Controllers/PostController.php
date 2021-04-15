<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Community;
use App\Models\Post;
use App\UseCases\Post\Create;
use App\UseCases\Post\Update;
use App\UseCases\Post\Delete;
use Illuminate\Http\Response;
use Throwable;

class PostController extends Controller
{
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
        $command              = new Create\Command();
        $command->communityId = $community->id;
        $command->userId      = (int) auth()->id();
        $command->title       = $request->get('title');
        $command->text        = $request->get('text');
        $command->url         = $request->get('url');

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

        $command              = new Update\Command();
        $command->id          = $post->id;
        $command->communityId = $community->id;
        $command->userId      = (int) auth()->id();
        $command->title       = $request->get('title');
        $command->text        = $request->get('text');
        $command->url         = $request->get('url');

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
            $handler = new Delete\Handler();
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

        return view('post.show', compact('community', 'post'));
    }
}
