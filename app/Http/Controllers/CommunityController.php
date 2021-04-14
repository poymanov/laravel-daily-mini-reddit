<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CommunityService;

class CommunityController extends Controller
{
    /** @var CommunityService */
    private CommunityService $communityService;

    /**
     * @param CommunityService $communityService
     */
    public function __construct(CommunityService $communityService)
    {
        $this->communityService = $communityService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $communities = $this->communityService->getAllLatest();

        return view('community.index', compact('communities'));
    }
}
