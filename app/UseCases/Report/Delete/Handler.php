<?php

declare(strict_types=1);

namespace App\UseCases\Report\Delete;

use App\Models\Report;
use App\Notifications\ReportResolved;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Notification;

class Handler
{
    /** @var UserService */
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Command $command
     *
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $report = Report::find($command->id);

        if (!$report) {
            throw new Exception('Failed to find report');
        }

        $report->delete();

        $reportAuthor = $this->userService->findUserById($report->user_id);

        if (!$reportAuthor) {
            throw new Exception('Failed to find report author');
        }

        Notification::send($reportAuthor, new ReportResolved($report));
    }
}
