<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PostVote\CreateRequest;
use App\Models\Community;
use App\Models\Post;
use App\UseCases\PostVote\Create;
use Exception;

class PostVoteController extends Controller
{
    /**
     * @param CreateRequest $request
     * @param Community     $community
     * @param Post          $post
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CreateRequest $request, Community $community, Post $post)
    {
        $this->authorize('vote', $post);

        $command = new Create\Command();
        $command->userId = (int) auth()->id();
        $command->communityId = $community->id;
        $command->postId = $post->id;
        $command->vote = $request->get('vote');

        try {
            $handler = new Create\Handler();
            $handler->handle($command);

            return redirect()->back()->with('success', 'Successfully voting');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Failed to voting');
        }
    }
}
