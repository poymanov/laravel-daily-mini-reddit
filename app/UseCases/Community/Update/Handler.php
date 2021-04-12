<?php

declare(strict_types=1);

namespace App\UseCases\Community\Update;

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
            throw new Exception('Failed to find community for update');
        }

        $community->name        = $command->name;
        $community->description = $command->description;

        if (!$community->save()) {
            throw new Exception('Failed to update community');
        }
    }
}
