<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\CreateRequest;
use App\Http\Requests\Community\UpdateRequest;
use App\Models\Community;
use App\Services\CommunityService;
use App\Services\UserService;
use App\UseCases\Community\Create;
use App\UseCases\Community\Update;
use App\UseCases\Community\Delete;
use Illuminate\Http\Response;
use Throwable;

class CommunityController extends Controller
{
    private CommunityService $communityService;
    private UserService $userService;

    /**
     * CommunityController constructor.
     * @param CommunityService $communityService
     * @param UserService $userService
     */
    public function __construct(CommunityService $communityService, UserService $userService)
    {
        $this->communityService = $communityService;
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index()
    {
        $userId = (int)auth()->id();

        $communities = $this->userService->isAdmin($userId) ?
            $this->communityService->getAll() :
            $this->communityService->getAllByUserId($userId);

        return view('profile.community.index', compact('communities'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('profile.community.create');
    }

    /**
     * @param CreateRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(CreateRequest $request)
    {
        $command = new Create\Command();
        $command->name = $request->get('name');
        $command->description = $request->get('description');
        $command->userId = (int)auth()->id();

        try {
            $handler = new Create\Handler();
            $handler->handle($command);

            return redirect(route('profile.communities.index'))->with('alert.success', 'Community created');
        } catch (Throwable $e) {
            return redirect(route('profile.communities.index'))->with('alert.error', 'Failed to create community');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        return new Response();
    }

    /**
     * @param Community $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Community $community)
    {
        $this->authorize('update', $community);

        return view('profile.community.edit', compact('community'));
    }

    /**
     * @param UpdateRequest $request
     * @param Community $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, Community $community)
    {
        $this->authorize('update', $community);

        $command = new Update\Command();
        $command->id = $community->id;
        $command->name = $request->get('name');
        $command->description = $request->get('description');

        try {
            $handler = new Update\Handler();
            $handler->handle($command);

            return redirect(route('profile.communities.index'))->with('alert.success', 'Community updated');
        } catch (Throwable $e) {
            return redirect(route('profile.communities.index'))->with('alert.error', 'Failed to update community');
        }
    }

    /**
     * @param Community $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Community $community)
    {
        $this->authorize('delete', $community);

        $command = new Delete\Command();
        $command->id = $community->id;

        try {
            $handler = new Delete\Handler();
            $handler->handle($command);

            return redirect(route('profile.communities.index'))->with('alert.success', 'Community deleted');
        } catch (Throwable $e) {
            return redirect(route('profile.communities.index'))->with('alert.error', 'Failed to delete community');
        }
    }
}
