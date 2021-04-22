<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Community;
use App\Services\CommunityService;
use App\Services\PostService;

class CommunityController extends Controller
{
    /** @var CommunityService */
    private CommunityService $communityService;

    /** @var PostService */
    private PostService $postService;

    /**
     * @param CommunityService $communityService
     * @param PostService      $postService
     */
    public function __construct(CommunityService $communityService, PostService $postService)
    {
        $this->communityService = $communityService;
        $this->postService      = $postService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $communities = $this->communityService->getAllLatest();

        return view('community.index', compact('communities'));
    }

    /**
     * @param Community $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Community $community)
    {
        $communityPostsTextPreviewLimit = config('custom.community_posts_text_preview_limit');

        $posts = $this->postService->getAllLatestByCommunityId($community->id, (int) auth()->id());

        return view('community.show', compact('community', 'posts', 'communityPostsTextPreviewLimit'));
    }
}
