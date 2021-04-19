<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PostService;

class HomeController extends Controller
{
    /** @var PostService */
    private PostService $postService;

    /**
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posts = $this->postService->getAllLatest();
        $homePostsTextPreviewLimit = config('custom.home_posts_text_preview_limit');

        return view('home', compact('posts', 'homePostsTextPreviewLimit'));
    }
}
