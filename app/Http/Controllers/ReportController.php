<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Report\StoreRequest;
use App\Services\ReportService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\UseCases\Report\Create;

class ReportController extends Controller
{
    /** @var ReportService */
    private ReportService $reportService;

    /** @var UserService */
    private UserService $userService;

    /**
     * @param ReportService $reportService
     * @param UserService   $userService
     */
    public function __construct(ReportService $reportService, UserService $userService)
    {
        $this->reportService = $reportService;
        $this->userService   = $userService;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $type = $request->get('type');
        $id   = $request->get('id');

        if (is_null($type) || is_null($id)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        try {
            $this->reportService->checkReportObject($type, (int) $id, (int) auth()->id());
        } catch (Exception $e) {
            return redirect()->route('home')->with('alert.error', $e->getMessage());
        }

        return view('report.create', compact('type', 'id'));
    }

    /**
     * @param StoreRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        try {
            $command         = new Create\Command();
            $command->text   = $request->get('text');
            $command->userId = (int) auth()->id();
            $command->type   = $request->get('type');
            $command->id     = (int) $request->get('id');

            $handler = new Create\Handler($this->reportService, $this->userService);
            $handler->handle($command);

            return redirect()->route('home')->with('alert.success', 'Report was successfully created');
        } catch (Exception $e) {
            return redirect()->back()->with('alert.error', $e->getMessage());
        }
    }
}
