<?php

declare(strict_types=1);

namespace App\UseCases\Post\Delete;

class Command
{
    /** @var int */
    public int $id;

    /** @var int */
    public int $userId;
}
