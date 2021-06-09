<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ReportController extends Controller
{
    private ReportService $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $reports = $this->reportService->getAll();

        return view('profile.report.index', compact('reports'));
    }
}
