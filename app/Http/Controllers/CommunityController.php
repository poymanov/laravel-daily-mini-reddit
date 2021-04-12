<?php

namespace App\Http\Controllers;

use App\Http\Requests\Community\CreateRequest;
use App\UseCases\Community\Create;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return new Response();
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

        return redirect(route('dashboard'));
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        return new Response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return new Response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        return new Response();
    }
}
