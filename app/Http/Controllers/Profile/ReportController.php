<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportService;
use App\Services\UserService;
use App\UseCases\Report\Delete;
use Throwable;

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $reports = $this->reportService->getAll();

        return view('profile.report.index', compact('reports'));
    }

    /**
     * @param Report $report
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function show(Report $report)
    {
        $reportableObject = $this->reportService->getReportObjectByReportId($report->id);

        return view('profile.report.show', compact('report', 'reportableObject'));
    }

    /**
     * @param Report $report
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Report $report)
    {
        $command     = new Delete\Command();
        $command->id = $report->id;

        try {
            $handler = new Delete\Handler($this->userService);
            $handler->handle($command);

            return redirect(route('profile.reports.index'))->with('alert.success', 'Report resolved');
        } catch (Throwable $e) {
            return redirect(route('profile.reports.index'))->with('alert.error', 'Failed to resolve report');
        }
    }
}
