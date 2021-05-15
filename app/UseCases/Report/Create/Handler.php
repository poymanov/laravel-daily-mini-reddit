<?php

declare(strict_types=1);

namespace App\UseCases\Report\Create;

use App\Models\Report;
use App\Services\ReportService;
use Exception;

class Handler
{
    /** @var ReportService */
    private ReportService $reportService;

    /**
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param Command $command
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $this->reportService->checkReportObject($command->type, $command->id, $command->userId);

        $reportableType = $this->reportService->getReportableTypeByType($command->type);

        $report                  = new Report();
        $report->text            = $command->text;
        $report->user_id         = $command->userId;
        $report->reportable_type = $reportableType;
        $report->reportable_id   = $command->id;

        if (!$report->save()) {
            throw new Exception('Failed to create report');
        }
    }
}
