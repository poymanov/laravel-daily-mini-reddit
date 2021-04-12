<?php

declare(strict_types=1);

namespace App\UseCases\Community\Create;

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
        $community              = new Community();
        $community->user_id     = $command->userId;
        $community->name        = $command->name;
        $community->description = $command->description;

        if (!$community->save()) {
            throw new Exception('Failed to create community');
        }
    }
}
