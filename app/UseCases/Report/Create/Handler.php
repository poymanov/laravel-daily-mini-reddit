<?php

declare(strict_types=1);

namespace App\UseCases\Report\Create;

use App\Models\Report;
use App\Models\User;
use App\Notifications\NewReport;
use App\Services\ReportService;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Notification;

class Handler
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

        Notification::send($this->userService->getAdminUsers(), new NewReport($report));
    }
}
