<?php

declare(strict_types=1);

namespace App\UseCases\Community\Delete;

use App\Models\Community;
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
        $community = Community::find($command->id);

        if (!$community) {
            throw new Exception('Failed to find community for delete');
        }

        $community->delete();
    }
}
