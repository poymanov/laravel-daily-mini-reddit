<?php

namespace App\Http\Controllers;

use App\Http\Requests\Community\CreateRequest;
use App\Http\Requests\Community\UpdateRequest;
use App\Models\Community;
use App\Services\CommunityService;
use App\UseCases\Community\Create;
use App\UseCases\Community\Update;
use App\UseCases\Community\Delete;
use Illuminate\Http\Response;

class CommunityController extends Controller
{
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
        $communities = $this->communityService->getAllByUserId((int) auth()->id());

        return view('community.index', compact('communities'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('community.create');
    }

    /**
     * @param CreateRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(CreateRequest $request)
    {
        $command              = new Create\Command();
        $command->name        = $request->get('name');
        $command->description = $request->get('description');
        $command->userId      = (int) auth()->id();

        $handler = new Create\Handler();
        $handler->handle($command);

        return redirect(route('communities.index'));
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

        return view('community.edit', compact('community'));
    }

    /**
     * @param UpdateRequest $request
     * @param Community     $community
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, Community $community)
    {
        $this->authorize('update', $community);

        $command              = new Update\Command();
        $command->id          = $community->id;
        $command->name        = $request->get('name');
        $command->description = $request->get('description');

        $handler = new Update\Handler();
        $handler->handle($command);

        return redirect(route('communities.index'));
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

        $command     = new Delete\Command();
        $command->id = $community->id;

        $handler = new Delete\Handler();
        $handler->handle($command);

        return redirect(route('communities.index'));
    }
}
