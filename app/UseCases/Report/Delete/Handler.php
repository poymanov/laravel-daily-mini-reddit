<?php

declare(strict_types=1);

namespace App\UseCases\Report\Delete;

use App\Models\Report;
use Exception;

class Handler
{
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
    }
}
