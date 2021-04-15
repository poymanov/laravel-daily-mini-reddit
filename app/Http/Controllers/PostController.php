<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreateRequest;
use App\Models\Community;
use App\UseCases\Post\Create;
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
}
