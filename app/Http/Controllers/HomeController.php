<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CommunityService;
use App\Services\PostService;

class HomeController extends Controller
{
    /** @var PostService */
    private PostService $postService;

    /** @var CommunityService */
    private CommunityService $communityService;

    /**
     * @param PostService      $postService
     * @param CommunityService $communityService
     */
    public function __construct(PostService $postService, CommunityService $communityService)
    {
        $this->postService      = $postService;
        $this->communityService = $communityService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posts                     = $this->postService->getAllLatest();
        $communities               = $this->communityService->getAllLatestWithLimit();
        $homePostsTextPreviewLimit = config('custom.home_posts_text_preview_limit');

        return view('home', compact('posts', 'homePostsTextPreviewLimit', 'communities'));
    }
}
